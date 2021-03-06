<?php
namespace App\Http\Controllers;
use App\Fundamental;

class FundamentalController extends Controller
{
    protected $metas = [
                                'total_no_securities',
                                'earning_per_share',
                                'year_end',
                                'net_asset_val_per_share',
                                'paid_up_capital',
                                'last_agm_held',
                                'authorized_capital',
                                'reserve_and_surp',
                                'total_no_securities',
                                'public_share_per',
                                'dsex_listed',
                                'ds30_listed',
                                'dses_listed',
                                'paid_up_capital',
                                'share_percentage_public',
                                'share_percentage_foreign',
                                'share_percentage_institute',
                                'share_percentage_govt',
                                'share_percentage_director',
                            ];


    public function index()
    {
        $data = Fundamental::where('is_latest', 1)
                            ->select('instrument_code as code', 'meta_key', 'meta_value', 'meta_date')
                            ->leftJoin('instruments', 'instruments.id', 'instrument_id')
                            ->leftJoin('metas', 'metas.id', 'meta_id')
                            ->whereIn('meta_key', $this->metas)
                            ->where('instrument_code', '!=', null)
                            ->get()->groupBy(['code', 'meta_key']);
        return $data;
    }

    public function history($symbol, $key)
    {
        $key = explode(',', $key);
        $groupBy = "meta_key";
        if(app('request')->has('groupBy')){
            $groupBy = app('request')->groupBy;
        }
        $data = Fundamental::whereIn('meta_key', $key)
                            ->select('instrument_code as code', 'meta_key', 'meta_value', 'meta_date')
                            ->leftJoin('instruments', 'instruments.id', 'instrument_id')
                            ->leftJoin('metas', 'metas.id', 'meta_id')
                            ->where('instrument_code', $symbol)
                            ->orderBy('meta_date', 'desc')
                            ->get()->groupBy(['code', $groupBy]);
        return $data;        
    }

    public function show($key)
    {
        $data = Fundamental::where('meta_key', $key)
                            ->where('is_latest', 1)
                            ->select('instrument_code as code', 'meta_key', 'meta_value', 'meta_date')
                            ->leftJoin('instruments', 'instruments.id', 'instrument_id')
                            ->leftJoin('metas', 'metas.id', 'meta_id')
                            ->where('instrument_code', '!=', null)
                            ->get()->groupBy(['code', 'meta_key']);
        return $data;      
    }
}
