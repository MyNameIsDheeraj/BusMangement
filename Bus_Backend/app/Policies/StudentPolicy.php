<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class StudentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role->permissions->pluck('name')->contains('view_student');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Student $student): bool
    {
        if ($user->role->name === 'admin') {
            return $user->role->permissions->pluck('name')->contains('view_student');
        } elseif ($user->role->name === 'teacher') {
            $classIds = $user->classTeacher ? $user->classTeacher->pluck('id') : collect();
            return $classIds->contains($student->class_id) && $user->role->permissions->pluck('name')->contains('view_student');
        } elseif ($user->role->name === 'parent') {
            $studentIds = $user->parent->students->pluck('id');
            return $studentIds->contains($student->id) && $user->role->permissions->pluck('name')->contains('view_student');
        } elseif ($user->role->name === 'student') {
            return $user->student && $user->student->id === $student->id && $user->role->permissions->pluck('name')->contains('view_student');
        }
        
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role->name === 'admin' && $user->role->permissions->pluck('name')->contains('create_student');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Student $student): bool
    {
        if ($user->role->name === 'admin') {
            return $user->role->permissions->pluck('name')->contains('edit_student');
        } elseif ($user->role->name === 'teacher') {
            $classIds = $user->classTeacher ? $user->classTeacher->pluck('id') : collect();
            return $classIds->contains($student->class_id) && $user->role->permissions->pluck('name')->contains('edit_student');
        } elseif ($user->role->name === 'parent') {
            $studentIds = $user->parent->students->pluck('id');
            return $studentIds->contains($student->id) && $user->role->permissions->pluck('name')->contains('edit_student');
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Student $student): bool
    {
        return $user->role->name === 'admin' && $user->role->permissions->pluck('name')->contains('delete_student');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Student $student): bool
    {
        return $user->role->name === 'admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Student $student): bool
    {
        return $user->role->name === 'admin';
    }
}
