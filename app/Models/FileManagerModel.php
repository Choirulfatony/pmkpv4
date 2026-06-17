<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class FileManagerModel extends Model
{
    protected $table = 'siimut_file_manager';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $allowedFields = [
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'uploaded_by',
        'description',
        'parent_id',
        'is_folder',
    ];

    public function getItems($parentId = null)
    {
        $db = db_connect();
        $builder = $db->table('siimut_file_manager fm');
        $builder->select('fm.*, up.profile_fullname as uploader_name')
            ->join('user_profile up', 'up.profile_id = fm.uploaded_by', 'left');

        if ($parentId === null) {
            $builder->where('fm.parent_id IS NULL');
        } else {
            $builder->where('fm.parent_id', (int) $parentId);
        }

        $builder->orderBy('fm.is_folder DESC, fm.file_name ASC');
        return $builder->get()->getResult();
    }

    public function getBreadcrumbs($id)
    {
        $crumbs = [];
        $current = $this->find($id);
        while ($current) {
            array_unshift($crumbs, $current);
            $current = $current->parent_id ? $this->find($current->parent_id) : null;
        }
        return $crumbs;
    }

}
