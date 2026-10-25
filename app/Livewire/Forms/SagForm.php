<?php


namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Models\Sager;
use App\Models\Debitorer;
use App\Models\Konsulenter;
use App\Models\Sagsbehandler;
use App\Models\Kreditorer;
use App\Models\Status;
use App\Models\Bemaerkning;
use App\Models\KTR;
use App\Models\afslutning;
use App\Models\udlaeg;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\SagerFieldSetting;
use Carbon\Carbon;

class SagForm extends Form
{
    public ?Sager $sag = null; // allow null to prevent typed property error

    public ?Debitorer $debitor;
    
    #[Validate('required|min:1')]
    public $sagsnr = '';

    #[Validate('required|min:1')]
    public $hovedstol = '';

    #[Validate('required|min:2')]
    public $navn = '';

    // Debiro fields
    public $adresse = '';

    public $kreditor_lotusID;
    public $kreditor_navn;    

    
    // 🧾 Debitor fields (must exist because they’re used in UpdateDebitor)
    #[Validate('nullable|string')]
    public $email;
    #[Validate('nullable|string')]
    public $email2;

    #[Validate('nullable|string')]
    public $tlf;
    #[Validate('nullable|string')]
    public $tlf2;

    #[Validate('nullable|string')]
    public $mobil; //samme som tlf#2
    
    public $postnr;
    public $co;
    public $pnr;
    public $by = 'Tryk pil ned for at vælge by eller skriv en værdi i postnummer feltet!';

    #[Validate('nullable|string')]
    public $stelnr = '';

    // Date fields
    #[Validate('nullable|date')]
    public $afsluttet = null;

    #[Validate('nullable|date')]
    public $modtaget = null;

    #[Validate('nullable|date')]
    public $betalt = null;

    #[Validate('nullable|date')]
    public $faktureret = null;

    #[Validate('nullable|date')]
    public $senesterapport = null;

    #[Validate('nullable|date')]
    public $fakturadato = null;

    #[Validate('nullable|date')]
    public $opgivet  = null;

    // Amount/numeric fields
    #[Validate('nullable|string')]
    public $renter = '';
    public $ktr = '';
    public $afslutning = '';
    public $sagsbehandler = null;
    public $sagsbehandler_text_only = '';

    #[Validate('nullable|string')]
    public $gebyr = '';


    public $ialt = '';

    #[Validate('nullable|string')]
    public $startgebyr = '';

    #[Validate('nullable|string')]
    public $restgaeld_dkg = '';
    #[Validate('nullable|string')]
    public $restgaeld_kreditor = '';
    #[Validate('nullable|string')]
    public $indbetalt = '';
    public $resterende = '';


    #[Validate('nullable|string')]
    public $n_mdlydelse = '';

    // Text/textarea fields
    
    public $konsulent = '';
    
    public $udlaeg = '';

    public $bemaerkning = '';
    public $status = '';

    #[Validate('nullable|string')]
    public $aktiv = '';
    
    #[Validate('nullable|string')]
    public $fakturanr = '';

    public array $readonlyFields = [];

    public $kreditor;
    #[Validate('nullable|string')]
    public $kort_bemaerkning='';
    #[Validate('nullable|string')]
    public $kontakt_bemaerkning='';
    #[Validate('nullable|string')]
    public $kode='';
    #[Validate('nullable|date')]
    public $dato = null;
    #[Validate('nullable|string')]
    public $adropl='';
    
    // Dynamic field array
    public array $data = [];
    
    public function setSag(?Sager $sag): void
    {
        if (!$sag) {
            return;
        }

        $data = [];

        foreach ($sag->getFillable() as $field) {
            $value = $sag->$field;

            // 🟢 Hvis værdien er en Carbon-instans eller en dato, så formater den altid til Y-m-d
            if ($value instanceof \Illuminate\Support\Carbon || $value instanceof \DateTime) {
                $value = $value->format('Y-m-d');
            } 
            // Hvis det er en streng der ligner en dato, så rens den
            elseif (in_array($field, ['afsluttet', 'modtaget', 'betalt', 'faktureret', 'senesterapport', 'fakturadato', 'opgivet', 'dato', 'adropl']) && !empty($value)) {
                try {
                    $value = \Carbon\Carbon::parse($value)->format('Y-m-d');
                } catch (\Exception $e) {
                    // Behold original hvis parsing fejler
                }
            }

            $data[$field] = $value;
        }

        $this->fill($data);
    }

    public function fillFromModel(Sager $sag): void
    {
        $this->fill($sag->only(array_keys($this->rules())));
    }

    public function SetDebitor(?Sager $sag = null)
    {
        if (!$sag) return;

        $debitor = $sag->debitor?->first();
        if (!$debitor) return;

        $this->debitor = $debitor;

        // Fyld formularen automatisk med felter der matcher
        $this->fill($debitor->only([
            'navn', 'adresse', 'pnr', 'tlf', 'tlf2', 'tlf3', 
            'mobil', 'email', 'email2', 'postnr', 'co', 
            'kontakt_bemaerkning', 'adropl', 'by'
        ]));
    }
    
    public function setRelation(string $relation, Sager $sag = null): void
    {
        $sag = $sag ?? $this->sag;
        if (!$sag) {
            \Log::debug("setRelation: No sag found for relation {$relation}");
            return;
        }

        // Normalize relation name
        $relation = strtolower($relation);
        $relationMethod = $relation;

        if (!method_exists($sag, $relationMethod)) {
            \Log::debug("setRelation: Method {$relationMethod} does not exist on " . get_class($sag));
            return;
        }

        // Get related model's table
        $relatedTable = $sag->$relationMethod()->getRelated()->getTable();

        // Use fully qualified ID column to avoid ambiguity
        $relatedIds = $sag->$relationMethod()
            ->select("{$relatedTable}.id")
            ->pluck("{$relatedTable}.id")
            ->toArray();

        $property = $relation;
        $this->$property = $relatedIds[0] ?? null;

        \Log::debug("setRelation: {$relation} => " . ($this->$property ?? 'null') . " from table {$relatedTable}");
    }

    public function updateRelation(string $property, int|Sager|null $sag = null): ?array
    {
        // 🟢 Hvis $sag er sendt som et ID (heltal), så hent Sager-modellen
        if (is_int($sag)) {
            $sag = Sager::find($sag);
        } else {
            $sag = $sag ?? $this->sag;
        }

        if (!$sag) {
            \Log::debug("updateRelation: No sag found with ID/instance provided");
            return null;
        }

        // Make sure the property exists on the form
        if (!property_exists($this, $property)) {
            \Log::debug("updateRelation: Property {$property} does not exist on form");
            return null;
        }

        $relationMethod = $property;
        
        if (!method_exists($sag, $relationMethod)) {
            \Log::debug("updateRelation: Relation method {$relationMethod} does not exist on Sager");
            return null;
        }

        // Hvis feltet er tomt, fjern relationen (detach), ellers sync
        if (empty($this->$property)) {
            $sag->$relationMethod()->detach();
            \Log::debug("updateRelation: Detached {$relationMethod}");
        } else {
            // Sync the pivot table with selected value
            $sag->$relationMethod()->sync([$this->$property]);
            \Log::debug("updateRelation: Synced {$relationMethod} with value {$this->$property}");
        }

        return [$this->$property];
    }

   public function SetKreditor(?Sager $sag = null): void
    {
        if (!$sag) {
            return;
        }

        // Tjek om relationen allerede er loadet, ellers hent den via relationen (ikke en ny Sager-find)
        $kreditor = $sag->relationLoaded('kreditor') 
            ? $sag->kreditor->first() 
            : $sag->kreditor()->first();

        if (!$kreditor) {
            return;
        }

        $this->kreditor = $kreditor->id;
        $this->kreditor_lotusID = $kreditor->lotusID;
        $this->kreditor_navn = $kreditor->navn;    
    }

   /**
     * Return the kreditor ID to sync (nullable)
     */
    public function UpdateKreditor(): ?int
    {
        if (!empty($this->kreditor)) {
            return (int) $this->kreditor;
        }

        return $this->sag?->kreditor->first()?->id ?? null;
    }

    /**
     * Return the sagsbehandler ID to sync (nullable)
     */
    public function UpdateSagsbehandler(): ?int
    {
        if (!empty($this->sagsbehandler)) {
            return (int) $this->sagsbehandler;
        }

        return $this->sag?->sagsbehandler->first()?->id ?? null;
        }
    
    public function CreateDebitor(): ?int
    {
        // Valider og rens data (så får du alle felter, inkl. de nye, med automatisk)
        $validated = $this->validateDebitor();

        $debitor = Debitorer::create($validated);

        return $debitor?->id;
    }
   
    public function validateDebitor(): array
    {
        $this->postnr = $this->postnr ?: null;
        $this->tlf    = $this->tlf ?: null;
        $this->tlf2   = $this->tlf2 ?: null;   // 👈 Tilføjet
        $this->mobil  = $this->mobil ?: null;
        $this->email  = $this->email ?: null;
        $this->email2 = $this->email2 ?: null; // 👈 Tilføjet
        $this->adresse = $this->adresse ?: null;
        $this->pnr    = $this->pnr ?: null;
        $this->co     = $this->co ?: null;
        $this->kontakt_bemaerkning     = $this->kontakt_bemaerkning ?: null;
        $this->adropl     = $this->adropl ?: null;

        // 🟢 TJEK FOR DUBLETTER PÅ TELEFONNUMRE
        // 🟢 TJEK FOR DUBLETTER PÅ TELEFONNUMRE (Inkl. mobil/tlf3)
        $phones = array_filter([$this->tlf, $this->tlf2, $this->mobil]);
        if (count($phones) !== count(array_unique($phones))) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'mobil' => 'Du kan ikke angive det samme telefonnummer flere gange.',
            ]);
        }

        // 🟢 TJEK FOR DUBLETTER PÅ E-MAILS
        $emails = array_filter([$this->email, $this->email2]);
        if (count($emails) !== count(array_unique($emails))) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'email2' => 'Du kan ikke angive den samme e-mailadresse flere gange.',
            ]);
        }

        return $this->validate([
            'navn'    => 'required|string|max:255',
            'adresse' => 'nullable|string|max:255',
            'co'      => 'nullable|string|max:255',
            'postnr'  => 'nullable|max:10',
            'pnr'     => 'nullable|max:50',
            'tlf'     => 'nullable|regex:/^[0-9]+$/|max:50',
            'tlf2'    => 'nullable|regex:/^[0-9]+$/|max:50', // 👈 Tilføjet
            'mobil'   => 'nullable|regex:/^[0-9]+$/|max:50',
            'email'   => 'nullable|email|max:255',
            'email2'  => 'nullable|email|max:255',           // 👈 Tilføjet
            'kontakt_bemaerkning'  => 'nullable|string|max:255',
            'adropl'     => 'nullable|date',                // (Bemærk: fjern '|max:255' på en dato-regel, da det kan give advarsler)
        ]);
    }

    public function UpdateDebitor(?Sager $sag): ?int
    {
        if (!$sag) {
            \Log::error('UpdateDebitor called with null $sag');
            return null;
        }

        // Validate debitor data
        $validated = $this->validateDebitor();

        // Load related Debitor(s)
        $sag->load('debitor');
        $existingDebitor = $sag->debitor->first();

        if ($existingDebitor) {
            $existingDebitor->update($validated);
            $sag->debitor()->sync([$existingDebitor->id]); // sync ensures only this debitor
            return $existingDebitor->id;
        }

        // Create a new Debitor if none exists
        $newDebitor = Debitorer::create($validated);

        if (!$newDebitor) {
            \Log::error('Failed to create new Debitor');
            return null;
        }

        $sag->debitor()->sync([$newDebitor->id]);
        return $newDebitor->id;
    }

    
    /**
     * Called from SagEditor — applies validated data to model
     */
    /**
     * Update or create the Sager and its relations.
     */
    public function update(?Sager $sag = null): Sager
    {
        // 1️⃣ Use passed Sager or fallback to current form Sager
        $sag = $sag ?? $this->sag ?? new Sager();
        
        $data = $this->validate();
        
        // 4️⃣ Merge defaults (date defaults null, boolean defaults true)
        $defaults = [
            'afsluttet' => $sag->afsluttet ?? null,
            'faktureret' => $sag->faktureret ?? null,
        ];

        $data = array_merge($defaults, $data);

        // 5️⃣ Fill model but do NOT save
        $sag->fill($data);

        // 6️⃣ Update internal reference
        $this->sag = $sag;

        return $sag;
    }

    public function toFilterArray(): array
    {
        return collect($this->all())
            ->filter(function ($value) {
                return $value !== null && $value !== '';
            })
            ->toArray();
    }
    
}