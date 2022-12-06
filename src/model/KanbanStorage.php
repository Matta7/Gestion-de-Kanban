<?php

interface KanbanStorage {
    public function read($id);
    public function readAll();
    public function create(Kanban $k);
    public function delete($id);
    public function update($id, $k);
    public function updateColumn($idColumn, $c);
    public function addMember($idKanban, $idMember);
    public function deleteMember($idKanban, $idMember);
}