<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model as MainModel;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;

/**
 * Class Model
 *
 * @property int $id
 * @property bool|null $status
 * @property string|null $createdBy
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Model newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Model newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Model query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Model whereAcademicYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Model whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Model whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Model whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Model whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Model inRandomOrder()
 *
 * Additional Methods:
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Model orderBy($column, $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Model limit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Model pluck($column, $key = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Model get()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Model first()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Model findOrFail($id)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Model find($id)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Model exists()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Model latest()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Model create($data)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Model where($column, $operator = null, $value = null, $boolean = 'and')
 *
 * Relationships:
 * @method \Illuminate\Database\Eloquent\Relations\BelongsToMany students()
 * @method \Illuminate\Database\Eloquent\Relations\HasMany courses()
 */

class Model extends MainModel
{
    public static function findOrCustomFail($id): \Illuminate\Database\Eloquent\Builder|\Illuminate\Http\JsonResponse|Model
    {
        try {
            return self::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            // Handle the exception
            // For example, return a custom response
            return sendErrorResponse($e, 404);
        }
    }
}
