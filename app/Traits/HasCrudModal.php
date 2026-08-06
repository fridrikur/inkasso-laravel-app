<?php

namespace App\Traits;

trait HasCrudModal
{
    public bool $showFormModal = false;
    public bool $showDeleteModal = false;
    
    public $editingId = null;
    public $deletingId = null;

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->resetForm();
        $this->editingId = null;
        $this->showFormModal = true;
    }

    public function openEditModal($id)
    {
        $this->resetValidation();
        $this->editingId = $id;
        $this->loadItemData($id);
        $this->showFormModal = true;
    }

    public function closeFormModal()
    {
        $this->showFormModal = false;
        $this->editingId = null;
        $this->resetForm();
    }

    public function confirmDeleteModal($id = null)
    {
        $this->confirmDelete($id);
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    // Skal implementeres i komponenten til nulstilling af felter
    abstract public function resetForm(): void;

    // Skal implementeres i komponenten til indlæsning af data ved redigering
    abstract public function loadItemData($id): void;
}