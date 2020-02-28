<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 */
class CorporateAction extends Model
{
	protected $table = "corporate_action";
	/*
	fields
	"id": 1090,
	"instrument_id": 171,
	"action": "cashdiv",
	"value": 12.5,
	"premium": 0,
	"record_date": "2013-05-14",
	"active": 1,
	"adjusted": 1,
	"updated": "2014-11-21 18:52:58"
	*/
	protected $visible = ["action", "record_date", "premium", "value"];

}