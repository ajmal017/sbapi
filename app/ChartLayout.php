<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ChartLayout extends Model
{
	use SoftDeletes;
	protected $appends = ['timestamp'];
	protected $visible = ['content', 'id', 'name', 'symbol', 'resolution', 'timestamp'];
	protected $fillable = ['content', 'id', 'name', 'symbol', 'resolution'];
	public function getTimestampAttribute()
	{
		return strtotime($this->updated_at);
	}
}
