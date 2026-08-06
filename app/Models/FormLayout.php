<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormLayout extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'model_type',
        'layout_data',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'layout_data' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user who created this layout
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope to get active layouts
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get layouts for specific model
     */
    public function scopeForModel($query, $modelType)
    {
        return $query->where('model_type', $modelType);
    }

    /**
     * Get parsed layout data
     */
    public function getParsedLayoutAttribute()
    {
        return $this->layout_data;
    }

    /**
     * Get grid dimensions
     */
    public function getGridDimensionsAttribute()
    {
        $data = $this->layout_data;
        return [
            'rows' => $data['grid_rows'] ?? 3,
            'columns' => $data['grid_columns'] ?? 4,
        ];
    }

    /**
     * Get containers with fields
     */
    public function getContainersAttribute()
    {
        $data = $this->layout_data;
        return $data['containers'] ?? [];
    }

    /**
     * Check if layout has fields
     */
    public function hasFields(): bool
    {
        $containers = $this->containers;
        
        foreach ($containers as $container) {
            if (!empty($container['fields'])) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get all fields from all containers
     */
    public function getAllFields(): array
    {
        $allFields = [];
        $containers = $this->containers;
        
        foreach ($containers as $container) {
            if (!empty($container['fields'])) {
                foreach ($container['fields'] as $field) {
                    $allFields[] = $field;
                }
            }
        }
        
        return $allFields;
    }

    /**
     * Generate form component name
     */
    public function getComponentNameAttribute(): string
    {
        return 'Generated' . $this->model_type . 'Form' . $this->id;
    }

    /**
     * Generate view name
     */
    public function getViewNameAttribute(): string
    {
        return 'livewire.generated.' . str_replace('_', '-', snake_case($this->component_name));
    }
}