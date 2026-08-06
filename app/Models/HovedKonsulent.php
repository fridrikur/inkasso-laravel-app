<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Konsulenter;

class HovedKonsulent extends Model
{
    protected $table = 'hoved_konsulent';

    protected $fillable = ['hoved_konsulent_id'];

    /**
     * The Hoved Konsulent relation (singleton)
     */
    public function konsulent()
        {
            return $this->belongsTo(
                Konsulenter::class,
                'hoved_konsulent_id'
            );
        }

    /**
     * Retrieve singleton row
     */
    public static function getConfig(): self
    {
        return self::query()->first() ?? self::create();
    }

    /**
     * Set the Hoved Konsulent
     */
    public static function setHoved(Konsulenter $konsulent): void
    {
        $config = self::getConfig();
        $config->update(['hoved_konsulent_id' => $konsulent->id]);
    }

    /**
     * Unset Hoved Konsulent
     */
    public static function unsetHoved(): void
    {
        $config = self::getConfig();
        $config->update(['hoved_konsulent_id' => null]);
    }

    /**
     * Get the current Hoved Konsulent (nullable)
     */
    public static function current(): ?Konsulenter
    {
        return self::getConfig()->konsulent;
    }
}
