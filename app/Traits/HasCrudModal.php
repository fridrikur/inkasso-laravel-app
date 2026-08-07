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

    public function openDeleteModal($id): void
    {
        $this->resetValidation();

        $this->deletingId = $id;

        $this->prepareDelete($id);

        $this->showDeleteModal = true;
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;

        $this->deletingId = null;
    }

    abstract public function resetForm(): void;

    abstract public function loadItemData($id): void;

    /**
     * Optional hook.
     */
    public function prepareDelete($id): void
    {
    }
}