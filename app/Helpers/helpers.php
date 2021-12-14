<?php

use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Backpack\Settings\app\Models\Setting;
use Illuminate\Pagination\LengthAwarePaginator;

if (!function_exists('parseCurrency')) {
    function parseCurrency($value)
    {
        if (intval($value) == $value) {
            $return = number_format($value, 0, ',', '.');
        } else {
            $return = number_format($value, 2, ',', '.');

            $return = rtrim($return, 0);
        }

        return $return;
    }
}

if (!function_exists('getNextId')) {
    function getNextId()
    {
        $user = backpack_user();

        $companyBranch = $user->current()->branch->id;

        $statement = DB::select('SELECT MAX(estimate_number) + 1 AS next_id FROM estimates WHERE company_branch_id = ' . $companyBranch);

        if ($statement[0]->next_id == null) {
            return 1;
        }

        return $statement[0]->next_id;
    }
}

if (!function_exists('arrayToObject')) {
    function arrayToObject($array)
    {
        $obj = new stdClass;
        foreach ($array as $k => $v) {
            if (strlen($k)) {
                if (is_array($v)) {
                    $obj->{$k} = arrayToObject($v);
                } else {
                    $obj->{$k} = $v;
                }
            }
        }
        return $obj;
    }
}

if (!function_exists('sanitizeNumber')) {
    function sanitizeNumber($number)
    {
        $number = str_replace('.', '', $number);
        $number = str_replace(',', '.', $number);

        return $number;
    }
}

if (!function_exists('currencyFormat')) {
    /**
     * Price format
     *
     * @param string $value amount
     * @param string $currency currency code (currencies table)
     * @param bool $symbol return amount with symbol or not
     * @return string price formatted
     */
    function currencyFormat(?string $value, string $currency, bool $symbol = false): string
    {
        if ($value === '' || $value === null) return '';

        $currency = Cache::remember('articles', 30, function () use ($currency) {
            return Currency::where('code', $currency)->firstOrFail();
        });

        $price = number_format(
            $value,
            $currency->precision,
            $currency->decimal_separator,
            $currency->thousand_separator
        );

        $price = $symbol ? $currency->symbol . $price : $price;

        return $price;
    }
}

if (!function_exists('determineSource')) {
    function determineSource(Request $request): string
    {
        if (strpos($request->path(), 'admin/') !== false) {
            return 'Admin';
        }

        return 'Front';
    }
}

if (!function_exists('paginate')) {
    function paginate($items, $perPage = 1, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}

if (!function_exists('defaultCurrency')) {
    function defaultCurrency($return_id = false)
    {
        $default_currency = 'CLP';

        if ($return_id) {
            //
        } else {
            if (Cache::has('default_currency')) {
                $default_currency = Cache::get('default_currency');
            } else {
                $default_currency = Setting::get('default_currency');
                Cache::put('default_currency', $default_currency, 30);
            }
        }

        return $default_currency;
    }
}


if (!function_exists('sanitizeRUT')) {
    function sanitizeRUT($uid)
    {
        return str_replace('.', '', $uid);
    }
}
 
if (!function_exists('rutWithoutDV')) {
    function rutWithoutDV($uid)
    {
        $uid = sanitizeRUT($uid);

        $pos = strpos($uid, '-');

        if(!$pos) {
            return $uid;
        }

        $uid = substr($uid, 0, $pos);

        return $uid;
    }
}
