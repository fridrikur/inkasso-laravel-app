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
use App\Models\Ktr;
use App\Models\afslutning;
use App\Models\Udlaeg;
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

    
    public $tlf;
    public $mobil;
    public $postnr;
    public $co;
    public $pnr;
    public $by = 'Odense';

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


    // 🧾 Debitor fields (must exist because they’re used in UpdateDebitor)
    
    public $email;
    
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

        $debitor = $sag->sagerdebitor->first();
        if (!$debitor) return;

        $this->debitor = $debitor;

        // Copy values into dynamic form data array
        $this->navn = $debitor->navn;
        $this->adresse = $debitor->adresse;
        $this->pnr = $debitor->pnr;
        $this->tlf = $debitor->tlf;
        $this->mobil = $debitor->mobil;
        $this->email = $debitor->email;
        $this->postnr = $debitor->postnr;
        $this->co = $debitor->co;
        $this->kontakt_bemaerkning = $debitor->kontakt_bemaerkning;
        $this->adropl = $debitor->adropl;
        $this->by = $debitor->by;
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
        $relationMethod = 'sager' . $relation;

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



    public function updateRelation(string $property, int $sagId): ?array
    {
        // Find the Sager object
        $sag = Sager::find($sagId);

        if (!$sag) {
            \Log::debug("updateRelation: No sag found with ID {$sagId}");
            return null;
        }

        // Make sure the property exists on the form
        if (!property_exists($this, $property) || empty($this->$property)) {
            \Log::debug("updateRelation: Property {$property} is empty or does not exist on form");
            return null;
        }

        // Build the relation method dynamically
        $relationMethod = 'sager' . ucfirst($property);
        
        if (!method_exists($sag, $relationMethod)) {
            \Log::debug("updateRelation: Relation method {$relationMethod} does not exist on Sager");
            return null;
        }

        // Sync the pivot table with selected value
        $sag->$relationMethod()->sync([$this->$property]);

        \Log::debug("updateRelation: Synced {$relationMethod} with value {$this->$property}");

        return [$this->$property];
    }

   public function SetKreditor(?Sager $sag = null): void
    {
        if (!$sag) {
            return;
        }

        // ✅ Load sag with kreditor relation
        $sag = Sager::with('sagerkreditor')->find($sag->id);
        if (!$sag || $sag->sagerkreditor->isEmpty()) {
            return;
        }

        // ✅ Get the first related Kreditor
        $kreditor = $sag->sagerkreditor->first();

        if ($kreditor) {
            // Optional: eager load counts or other relations
            $kreditor = Kreditorer::withCount('sager')->find($kreditor->id);

            // ✅ Assign the kreditor ID to the form property
            $this->kreditor = $kreditor->id;
            $this->kreditor_lotusID = $kreditor->lotusID;
            $this->kreditor_navn = $kreditor->navn;    
        }
    }

   /**
     * Return the kreditor ID to sync (nullable)
     */
    public function UpdateKreditor(): ?int
    {
        if (!empty($this->kreditor)) {
            return (int) $this->kreditor;
        }

        return $this->sag?->sagerkreditor->first()?->id ?? null;
    }

    /**
     * Return the sagsbehandler ID to sync (nullable)
     */
    public function UpdateSagsbehandler(): ?int
    {
        if (!empty($this->sagsbehandler)) {
            return (int) $this->sagsbehandler;
        }

        return $this->sag?->sagersagsbehandler->first()?->id ?? null;
        }
    

    
    public function CreateDebitor()
    {
        $debitor = Debitorer::create($this->only(['navn', 'adresse','pnr','tlf','mobil','email','postnr','co','kontakt_bemaerkning','adresse_soegning','adropl']));
        $id = $debitor->id;
        return $id;
    }
   
    public function validateDebitor(): array
    {
        $this->postnr = $this->postnr ?: null;
        $this->tlf    = $this->tlf ?: null;
        $this->mobil  = $this->mobil ?: null;
        $this->email  = $this->email ?: null;
        $this->adresse = $this->adresse ?: null;
        $this->pnr    = $this->pnr ?: null;
        $this->co     = $this->co ?: null;
        $this->kontakt_bemaerkning     = $this->kontakt_bemaerkning ?: null;
        $this->adropl     = $this->adropl ?: null;
        return $this->validate([
            'navn'    => 'required|string|max:255',
            'adresse' => 'nullable|string|max:255',
            'co'      => 'nullable|string|max:255',
            'postnr'  => 'nullable|max:10',
            'pnr'     => 'nullable|max:50',
            'tlf'     => 'nullable|regex:/^[0-9]+$/|max:50',
            'mobil'   => 'nullable|regex:/^[0-9]+$/|max:50',
            'email'   => 'nullable|email|max:255',
            'kontakt_bemaerkning'  => 'nullable|string|max:255',
            'adropl'     => 'nullable|date|max:255',
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
    $sag->load('sagerdebitor');
    $existingDebitor = $sag->sagerdebitor->first();

    if ($existingDebitor) {
        $existingDebitor->update($validated);
        $sag->sagerdebitor()->sync([$existingDebitor->id]); // sync ensures only this debitor
        return $existingDebitor->id;
    }

    // Create a new Debitor if none exists
    $newDebitor = Debitorer::create($validated);

    if (!$newDebitor) {
        \Log::error('Failed to create new Debitor');
        return null;
    }

    $sag->sagerdebitor()->sync([$newDebitor->id]);
    return $newDebitor->id;
}

    public function SelectStatus(){
        $status_id = $this->status;
        if($status_id != null){
            $status = Status::withCount('sagerstatus')->where('id',$status_id)->first();
            $this->status = $status;
            $this->status = $status->id;
            return($this->status = $status->id);
        }
        else{
            $status = Status::withCount('sagerstatus')->first();
            if($status != null){
                $find_status = $this->status = Status::find($status->id);
                $this->status = $find_status->id;
                return($this->status = $find_status->id);
            }
        }
    }
    
    


    public function SelectBemaerkning(){
        $bemaerkning_id = $this->bemaerkning;
        if($bemaerkning_id != null){
            $bemaerkning = bemaerkning::withCount('sagerbemaerkning')->where('id',$bemaerkning_id)->first();
            $this->bemaerkning = $bemaerkning;
            $this->bemaerkning = $bemaerkning->id;
            return($this->bemaerkning = $bemaerkning->id);
        }
        else{
            $bemaerkning = bemaerkning::withCount('sagerbemaerkning')->first();
            if($bemaerkning != null){
                $find_bemaerkning = $this->bemaerkning = bemaerkning::find($bemaerkning->id);
                $this->bemaerkning = $find_bemaerkning->id;
                return($this->bemaerkning = $find_bemaerkning->id);
            }
        }
    }
    
    
    public function Selectafslutning(){
        $afslutning_id = $this->afslutning;
        if($afslutning_id != null){
            $afslutning = afslutning::withCount('sagerafslutning')->where('id',$afslutning_id)->first();
            $this->afslutning = $afslutning;
            $this->afslutning = $afslutning->id;
            return($this->afslutning = $afslutning->id);
        }
        else{
            $afslutning = afslutning::withCount('sagerafslutning')->first();
            if($afslutning != null){
                $find_afslutning = $this->afslutning = afslutning::find($afslutning->id);
                $this->afslutning = $find_afslutning->id;
                return($this->afslutning = $find_afslutning->id);
            }
        }
    }
    
    




    public function SelectKtr(){
        $ktr_id = $this->ktr;
        if($ktr_id != null){
            $ktr = ktr::withCount('sagerktr')->where('id',$ktr_id)->first();
            $this->ktr = $ktr;
            $this->ktr = $ktr->id;
            return($this->ktr = $ktr->id);
        }
        else{
            $ktr = ktr::withCount('sagerktr')->first();
            if($ktr != null){
                $find_ktr = $this->ktr = ktr::find($ktr->id);
                $this->ktr = $find_ktr->id;
                return($this->ktr = $find_ktr->id);
            }
        }
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