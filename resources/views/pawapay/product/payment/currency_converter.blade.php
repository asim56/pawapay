@extends('layouts.app')

@section('content')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
            <h2 class="text-2xl font-bold text-center mb-2">{{$product->name}}</h2>
            <p class="text-xl font-semibold text-center mb-6"><span id="sp_final_amount"></span></p>

            @if (session('error'))
                <div class="mb-4 text-sm text-red-600 bg-red-100 p-3 rounded-md border border-red-300">
                    {{ session('error') }}
                </div>
            @endif

            <form id="payment-form" method="POST" action="{{ route('product_payment') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="email">Phone Number</label>
                    <input type="phone" id="phone" name="phone" required
                           class="w-full border border-gray-300 rounded-md px-4 py-2 focus:ring focus:ring-blue-200">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="email">Email Address</label>
                    <input type="email" id="email" name="email" required
                           class="w-full border border-gray-300 rounded-md px-4 py-2 focus:ring focus:ring-blue-200">
                </div>

                <div class="mb-4">
                    <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                    <select id="country" name="country" required
                            class="w-full border border-gray-300 rounded-md px-4 py-2 bg-white focus:ring focus:ring-blue-200">
                        <option value="BEN" data-currency="XOF">🇧🇯 Benin</option>
                        <option value="BFA" data-currency="XOF">🇧🇫 Burkina Faso</option>
                        <option value="CMR" data-currency="XAF">🇨🇲 Cameroon</option>
                        <option value="CIV" data-currency="XOF">🇨🇮 Côte d’Ivoire</option>
                        <option value="COD" data-currency="CDF">🇨🇩 Democratic Republic of the Congo (DRC)</option>
                        <option value="GAB" data-currency="XAF">🇬🇦 Gabon</option>
                        <option value="GHA" data-currency="GHS">🇬🇭 Ghana</option>
                        <option value="KEN" data-currency="KES">🇰🇪 Kenya</option>
                        <option value="MWI" data-currency="MWK">🇲🇼 Malawi</option>
                        <option value="MOZ" data-currency="MZN">🇲🇿 Mozambique</option>
                        <option value="NGA" data-currency="NGN">🇳🇬 Nigeria</option>
                        <option value="COG" data-currency="XAF">🇨🇬 Republic of the Congo</option>
                        <option value="RWA" data-currency="RWF">🇷🇼 Rwanda</option>
                        <option value="SEN" data-currency="XOF">🇸🇳 Senegal</option>
                        <option value="SLE" data-currency="SLE">🇸🇱 Sierra Leone</option>
                        <option value="TZA" data-currency="TZS">🇹🇿 Tanzania</option>
                        <option value="UGA" data-currency="UGX">🇺🇬 Uganda</option>
                        <option value="ZMB" data-currency="ZMW">🇿🇲 Zambia</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="currency" class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
                    <select id="currency" name="currency" required
                            class="w-full border border-gray-300 rounded-md px-4 py-2 bg-white focus:ring focus:ring-blue-200">
                        <option value="XOF">XOF (West African CFA franc)</option>
                        <option value="XAF">XAF (Central African CFA franc)</option>
                        <option value="CDF">CDF (Congolese Franc)</option>
                        <option value="GHS">GHS (Ghanaian Cedi)</option>
                        <option value="KES">KES (Kenyan Shilling)</option>
                        <option value="MWK">MWK (Malawian Kwacha)</option>
                        <option value="MZN">MZN (Mozambican Metical)</option>
                        <option value="NGN">NGN (Nigerian Naira)</option>
                        <option value="RWF">RWF (Rwandan Franc)</option>
                        <option value="SLE">SLE (Sierra Leonean Leone)</option>
                        <option value="TZS">TZS (Tanzanian Shilling)</option>
                        <option value="UGX">UGX (Ugandan Shilling)</option>
                        <option value="ZMW">ZMW (Zambian Kwacha)</option>
                    </select>
                </div>
                <input type="hidden" name="product_price" id="product_price" value="{{$product->price}}">
                <input type="hidden" name="final_amount" id="final_amount" value="">
                <input type="hidden" name="reference_id" id="reference_id" value="{{$product->reference_id}}">

                <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-md transition">
                    Pay
                </button>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
<script>
    const currencyMap = {
        'BEN': { code: 'XOF', name: 'West African CFA franc', decimal_supported: false },
        'BFA': { code: 'XOF', name: 'West African CFA franc', decimal_supported: false },
        'CMR': { code: 'XAF', name: 'Central African CFA franc', decimal_supported: false },
        'CIV': { code: 'XOF', name: 'West African CFA franc', decimal_supported: false },
        'COD': { code: 'CDF', name: 'Congolese Franc', decimal_supported: false },
        'GAB': { code: 'XAF', name: 'Central African CFA franc', decimal_supported: false },
        'GHA': { code: 'GHS', name: 'Ghanaian Cedi', decimal_supported: false },
        'KEN': { code: 'KES', name: 'Kenyan Shilling', decimal_supported: false },
        'MWI': { code: 'MWK', name: 'Malawian Kwacha', decimal_supported: false },
        'MOZ': { code: 'MZN', name: 'Mozambican Metical', decimal_supported: false },
        'NGA': { code: 'NGN', name: 'Nigerian Naira', decimal_supported: false },
        'COG': { code: 'XAF', name: 'Central African CFA franc', decimal_supported: false },
        'RWA': { code: 'RWF', name: 'Rwandan Franc', decimal_supported: false },
        'SEN': { code: 'XOF', name: 'West African CFA franc', decimal_supported: false },
        'SLE': { code: 'SLE', name: 'Sierra Leonean Leone', decimal_supported: false },
        'TZA': { code: 'TZS', name: 'Tanzanian Shilling', decimal_supported: false },
        'UGA': { code: 'UGX', name: 'Ugandan Shilling', decimal_supported: false },
        'ZMB': { code: 'ZMW', name: 'Zambian Kwacha', decimal_supported: false }
    };

    $('#country').on('change', function () {
        const selectedCountry = $(this).val();
        const currency = currencyMap[selectedCountry];

        if (currency) {
            $('#currency').val(currency.code);
        } else {
            $('#currency').val();
        }
    });


    function convertUSDToSelectedCurrency() {
        const usdAmount = parseFloat($('#product_price').val());
        const targetCurrency = $('#currency').val();

        if (!usdAmount || !targetCurrency) return;

        $.ajax({
            url: `https://api.exchangerate.host/convert?access_key=d3d453b2a81d2668a138eeac09b7576f&from=USD&to=${targetCurrency}&amount=${usdAmount}`,
            method: 'GET',
            success: function (response) {
                if (response.result) {
                    const finalValue = Math.ceil(parseFloat(response.result.toFixed(2)));
                    $('#final_amount').val(finalValue);
                    $("#sp_final_amount").text(finalValue + " "+ $('#currency').val());
                } else {
                    $('#final_amount').val('Error');
                }
            },
            error: function () {
                $('#final_amount').val('Error');
            }
        });
    }

    // Trigger on currency change or USD amount change
    $('#country, #currency').on('change keyup', function () {
        convertUSDToSelectedCurrency();
    });

    // Optional: convert on page load
    $(document).ready(function () {
        $('#country').trigger('change');
        convertUSDToSelectedCurrency();
    });
</script>
@endsection
