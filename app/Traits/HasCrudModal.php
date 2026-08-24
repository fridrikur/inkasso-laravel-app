<?php

namespace App\Traits;

trait HasCrudModal
{
    public bool $showFormModal = false;
    public bool $showDeleteModal = false;

    public $editingId = null;
    public $deletingId = null;

    public function openCreateModal(): void
    {
        $this->resetValidation();
        $this->resetForm();

        $this->editingId = null;
        $this->showFormModal = true;
    }

    public function openEditModal($id): void
    {
        $this->resetValidation();

        $this->editingId = $id;

        $this->loadItemData($id);

        $this->showFormModal = true;
    }

    public function closeFormModal(): void
    {
        $this->showFormModal = false;
        $this->editingId = null;

        $this->resetForm();
    }

    public function confirmDeleteModal($id = null): void
    {
        $this->resetValidation();

        $this->deletingId = $id;

        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingId = null;

        $this->resetValidation();
    }

    abstract public function resetForm(): void;

    abstract public function loadItemData($id): void;
}