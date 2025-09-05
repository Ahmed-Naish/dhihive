<?php

namespace App\Repositories;

use Mail;
use App\Models\Settings\Currency;
use App\Models\Traits\CompanyTrait;
use Illuminate\Support\Facades\Auth;
use App\Helpers\CoreApp\Traits\FileHandler;
use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;
use JasonGuru\LaravelMakeRepository\Repository\BaseRepository;

class CurrencyRepository extends BaseRepository
{
    use FileHandler, CompanyTrait, ApiReturnFormatTrait;

    public function model()
    {
        return Currency::class;
    }

    function fields()
    {
        return [
            _trans('common.ID'),
            _trans('common.Name'),
            _trans('common.Code'),
            _trans('common.Symbol'),
            _trans('common.Action')
        ];
    }

    function table($request)
    {
        $data = $this->model->query()->where("company_id", Auth::user()->company_id);

        if ($request->search) {
            $data = $data->where('name', 'like', '%' . $request->search . '%');
        }

        $data = $data->orderBy("created_at", "desc")->paginate($request->limit ?? 2);

        return [
            'data' => $data->map(function ($data) {
                $action_button = '';
                    $action_button .= '<a href="' . route('manage.settings.edit_currency', $data->id) . '" class="dropdown-item"> ' . _trans('common.Edit') . '</a>';

                $button = ' <div class="dropdown dropdown-action">
                                       <button type="button" class="btn-dropdown" data-bs-toggle="dropdown"
                                           aria-expanded="false">
                                           <i class="fa-solid fa-ellipsis"></i>
                                       </button>
                                       <ul class="dropdown-menu dropdown-menu-end">
                                       ' . $action_button . '
                                       </ul>
                                   </div>';

                return [
                    'id' => $data->id,
                    'name' => @$data->name,
                    'code' => @$data->code,
                    'symbol' => @$data->symbol,
                    'action'   => $button
                ];
            }),
            'pagination' => [
                'total' => $data->total(),
                'count' => $data->count(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
                'total_pages' => $data->lastPage(),
                'pagination_html' =>  $data->links('backend.pagination.custom')->toHtml(),
            ],
        ];
    }

    public function getCurrencyData()
    {
        return [
            [1, 'Leke', 'ALL', 'Lek'],
            [2, 'Dollars', 'USD', '$'],
            [3, 'Afghanis', 'AFN', '؋'],
            [4, 'Pesos', 'ARS', '$'],
            [5, 'Guilders', 'AWG', 'ƒ'],
            [6, 'Dollars', 'AUD', '$'],
            [7, 'New Manats', 'AZN', 'ман'],
            [8, 'Dollars', 'BSD', '$'],
            [9, 'Dollars', 'BBD', '$'],
            [10, 'Rubles', 'BYR', 'p.'],
            [11, 'Euro', 'EUR', '€'],
            [12, 'Dollars', 'BZD', 'BZ$'],
            [13, 'Dollars', 'BMD', '$'],
            [14, 'Bolivianos', 'BOB', '$b'],
            [15, 'Convertible Marka', 'BAM', 'KM'],
            [16, 'Pula', 'BWP', 'P'],
            [17, 'Leva', 'BGN', 'лв'],
            [18, 'Reais', 'BRL', 'R$'],
            [19, 'Pounds', 'GBP', '£'],
            [20, 'Dollars', 'BND', '$'],
            [21, 'Riels', 'KHR', '៛'],
            [22, 'Dollars', 'CAD', '$'],
            [23, 'Dollars', 'KYD', '$'],
            [24, 'Pesos', 'CLP', '$'],
            [25, 'Yuan Renminbi', 'CNY', '¥'],
            [26, 'Pesos', 'COP', '$'],
            [27, 'Colón', 'CRC', '₡'],
            [28, 'Kuna', 'HRK', 'kn'],
            [29, 'Pesos', 'CUP', '₱'],
            [30, 'Koruny', 'CZK', 'Kč'],
            [31, 'Kroner', 'DKK', 'kr'],
            [32, 'Pesos', 'DOP ', 'RD$'],
            [33, 'Dollars', 'XCD', '$'],
            [34, 'Pounds', 'EGP', '£'],
            [35, 'Colones', 'SVC', '$'],
            [36, 'Pounds', 'FKP', '£'],
            [37, 'Dollars', 'FJD', '$'],
            [38, 'Cedis', 'GHC', '¢'],
            [39, 'Pounds', 'GIP', '£'],
            [40, 'Quetzales', 'GTQ', 'Q'],
            [41, 'Pounds', 'GGP', '£'],
            [42, 'Dollars', 'GYD', '$'],
            [43, 'Lempiras', 'HNL', 'L'],
            [44, 'Dollars', 'HKD', '$'],
            [45, 'Forint', 'HUF', 'Ft'],
            [46, 'Kronur', 'ISK', 'kr'],
            [47, 'Rupees', 'INR', '₹'],
            [48, 'Rupiahs', 'IDR', 'Rp'],
            [49, 'Rials', 'IRR', '﷼'],
            [50, 'Pounds', 'IMP', '£'],
            [51, 'New Shekels', 'ILS', '₪'],
            [52, 'Dollars', 'JMD', 'J$'],
            [53, 'Yen', 'JPY', '¥'],
            [54, 'Pounds', 'JEP', '£'],
            [55, 'Tenge', 'KZT', 'лв'],
            [56, 'Won', 'KPW', '₩'],
            [57, 'Won', 'KRW', '₩'],
            [58, 'Soms', 'KGS', 'лв'],
            [59, 'Kips', 'LAK', '₭'],
            [60, 'Lati', 'LVL', 'Ls'],
            [61, 'Pounds', 'LBP', '£'],
            [62, 'Dollars', 'LRD', '$'],
            [63, 'Switzerland Francs', 'CHF', 'CHF'],
            [64, 'Litai', 'LTL', 'Lt'],
            [65, 'Denars', 'MKD', 'ден'],
            [66, 'Ringgits', 'MYR', 'RM'],
            [67, 'Rupees', 'MUR', '₨'],
            [68, 'Pesos', 'MXN', '$'],
            [69, 'Tugriks', 'MNT', '₮'],
            [70, 'Meticais', 'MZN', 'MT'],
            [71, 'Dollars', 'NAD', '$'],
            [72, 'Rupees', 'NPR', '₨'],
            [73, 'Guilders', 'ANG', 'ƒ'],
            [74, 'Dollars', 'NZD', '$'],
            [75, 'Cordobas', 'NIO', 'C$'],
            [76, 'Nairas', 'NGN', '₦'],
            [77, 'Krone', 'NOK', 'kr'],
            [78, 'Rials', 'OMR', '﷼'],
            [79, 'Rupees', 'PKR', '₨'],
            [80, 'Balboa', 'PAB', 'B/.'],
            [81, 'Guarani', 'PYG', 'Gs'],
            [82, 'Nuevos Soles', 'PEN', 'S/.'],
            [83, 'Pesos', 'PHP', 'Php'],
            [84, 'Zlotych', 'PLN', 'zł'],
            [85, 'Rials', 'QAR', '﷼'],
            [86, 'New Lei', 'RON', 'lei'],
            [87, 'Rubles', 'RUB', 'руб'],
            [88, 'Pounds', 'SHP', '£'],
            [89, 'Riyals', 'SAR', '﷼'],
            [90, 'Dinars', 'RSD', 'Дин.'],
            [91, 'Rupees', 'SCR', '₨'],
            [92, 'Dollars', 'SGD', '$'],
            [93, 'Dollars', 'SBD', '$'],
            [94, 'Shillings', 'SOS', 'S'],
            [95, 'Rand', 'ZAR', 'R'],
            [96, 'Rupees', 'LKR', '₨'],
            [97, 'Kronor', 'SEK', 'kr'],
            [98, 'Dollars', 'SRD', '$'],
            [99, 'Pounds', 'SYP', '£'],
            [100, 'New Dollars', 'TWD', 'NT$'],
            [101, 'Baht', 'THB', '฿'],
            [102, 'Dollars', 'TTD', 'TT$'],
            [103, 'Lira', 'TRY', 'TL'],
            [104, 'Liras', 'TRL', '£'],
            [105, 'Dollars', 'TVD', '$'],
            [106, 'Hryvnia', 'UAH', '₴'],
            [107, 'Pesos', 'UYU', '$U'],
            [108, 'Sums', 'UZS', 'лв'],
            [109, 'Bolivares Fuertes', 'VEF', 'Bs'],
            [110, 'Dong', 'VND', '₫'],
            [111, 'Rials', 'YER', '﷼'],
            [112, 'Taka', 'BDT', '৳'],
            [113, 'Zimbabwe Dollars', 'ZWD', 'Z$'],
            [114, 'Kenya', 'KES', 'KSh'],
            [115, 'Nigeria', 'naira', '₦'],
            [116, 'Ghana', 'GHS', 'GH₵'],
            [117, 'Ethiopian', 'ETB', 'Br'],
            [118, 'Tanzania', 'TZS', 'TSh'],
            [119, 'Uganda', 'UGX', 'USh'],
            [120, 'Rwandan', 'FRW', 'FRw'],
            [121, 'UAE Dirham', 'AED', 'د.إ'],
        ];
    }
}
