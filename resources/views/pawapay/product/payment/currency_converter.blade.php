@extends('layouts.app')

@section('content')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <div class="min-h-screen bg-gray-100 flex items-center justify-center py-10 px-4">
        <div class="bg-white rounded-lg shadow-lg flex flex-col md:flex-row w-full max-w-5xl overflow-hidden">
            {{-- Product Info Left --}}
            <div class="md:w-1/2 p-8 border-r">
                <h2 class="text-2xl font-bold mb-1">{{$product->name}}</h2>
                <p class="text-gray-500 mb-6"></p>
                <div class="flex justify-center mb-6">
                    <img src="{{ asset('storage/' . $product->image) }}" alt="Product Image"
                         class="rounded-md shadow-md w-64">
                </div>
                <div class="text-2xl font-bold mb-4">
                    <span id="sp1_final_amount"></span>
                </div>

                <div class="text-sm text-gray-600 border-t pt-4 space-y-1">
                    <div class="flex justify-between">
                        <span>Subtotal:</span>
                        <span id="sp_product_price"> {{$product->product_price}} (USD)</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Fees:</span>
                        <span id="sp_product_fee"> {{$product->product_fee}} (USD)</span>
                    </div>
                    <div class="flex justify-between font-semibold text-gray-900 border-t pt-2">
                        <span>Total:</span>
                        <span id="sp_final_amount_total">{{$product->price}} (USD)</span>
                    </div>
                </div>
            </div>

            {{-- Payment Form Right --}}
            <div class="md:w-1/2 p-8">
                <h2 class="text-lg font-semibold mb-6">Payment Information</h2>

                @if (session('error'))
                    <div class="mb-4 text-sm text-red-600 bg-red-100 p-3 rounded-md border border-red-300">
                        {{ session('error') }}
                    </div>
                @endif

                <form id="payment-form" method="POST" action="{{ route('pay') }}">
                    @csrf

                    {{-- Phone Number --}}
                    {{--                    <div class="mb-4">--}}
                    {{--                        <label class="block text-sm font-medium text-gray-700 mb-1" for="phone">Phone Number *</label>--}}
                    {{--                        <input type="phone" id="phone" name="phone" required--}}
                    {{--                               class="w-full border border-gray-300 rounded-md px-4 py-2 focus:ring focus:ring-blue-200">--}}
                    {{--                    </div>--}}

                    {{--                    <div class="mb-4">--}}
                    {{--                        <label class="block text-sm font-medium text-gray-700 mb-1" for="country">Country *</label>--}}
                    {{--                        <select id="country" name="country" required--}}
                    {{--                                class="w-full border border-gray-300 rounded-md px-4 py-2 bg-white focus:ring focus:ring-blue-200">--}}
                    {{--                            <option value="">Select country</option>--}}
                    {{--                            --}}{{-- Country options --}}
                    {{--                            <option value="BEN" data-currency="XOF">🇧🇯 Benin</option>--}}
                    {{--                            <option value="BFA" data-currency="XOF">🇧🇫 Burkina Faso</option>--}}
                    {{--                            <option value="CMR" data-currency="XAF">🇨🇲 Cameroon</option>--}}
                    {{--                            <option value="CIV" data-currency="XOF">🇨🇮 Côte d’Ivoire</option>--}}
                    {{--                            <option value="COD" data-currency="CDF">🇨🇩 Democratic Republic of the Congo(CDF)</option>--}}
                    {{--                            <option value="USD" data-currency="USD" selected="selected">🇨🇩 Democratic Republic of the Congo(USD)</option>--}}
                    {{--                            <option value="GAB" data-currency="XAF">🇬🇦 Gabon</option>--}}
                    {{--                            <option value="GHA" data-currency="GHS">🇬🇭 Ghana</option>--}}
                    {{--                            <option value="KEN" data-currency="KES">🇰🇪 Kenya</option>--}}
                    {{--                            <option value="MWI" data-currency="MWK">🇲🇼 Malawi</option>--}}
                    {{--                            <option value="MOZ" data-currency="MZN">🇲🇿 Mozambique</option>--}}
                    {{--                            <option value="NGA" data-currency="NGN">🇳🇬 Nigeria</option>--}}
                    {{--                            <option value="COG" data-currency="XAF">🇨🇬 Republic of the Congo</option>--}}
                    {{--                            <option value="RWA" data-currency="RWF">🇷🇼 Rwanda</option>--}}
                    {{--                            <option value="SEN" data-currency="XOF">🇸🇳 Senegal</option>--}}
                    {{--                            <option value="SLE" data-currency="SLE">🇸🇱 Sierra Leone</option>--}}
                    {{--                            <option value="TZA" data-currency="TZS">🇹🇿 Tanzania</option>--}}
                    {{--                            <option value="UGA" data-currency="UGX">🇺🇬 Uganda</option>--}}
                    {{--                            <option value="ZMB" data-currency="ZMW">🇿🇲 Zambia</option>--}}
                    {{--                        </select>--}}
                    {{--                    </div>--}}

                    <div class="mb-4">
                        <select name="country" id="country" class="select2-country" required>
                            <option value="">Select country</option>
                            @foreach ($countries as $country)
                                <option value="{{ $country['alpha3'] }}"
                                        data-currency="{{ $country['currency'] }}"
                                        data-dial-code="{{ $country['dial_code'] }}"
                                        data-flag="{{ strtolower($country['alpha2']) }}"
                                    {{ $country['alpha3'] === "COD" ? 'selected' : '' }}>
                                    {{ $country['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1" for="currency">Currency</label>
                        <select id="currency" name="currency" required
                                class="w-full border border-gray-300 rounded-md px-4 py-2 bg-white focus:ring focus:ring-blue-200">
                            <option value="XOF">XOF (West African CFA franc)</option>
                            <option value="XAF">XAF (Central African CFA franc)</option>
                            <option value="CDF">CDF (Congolese Franc)</option>
                            <option value="USD" selected="selected">USD</option>
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

{{--                    <div class="mb-4">--}}
{{--                        <label class="block text-sm font-medium text-gray-700 mb-1" for="phone">Phone Number *</label>--}}
{{--                        <div class="flex gap-2">--}}
{{--                            <select name="country_code" id="country_code" required--}}
{{--                                    class="w-1/3 border border-gray-300 rounded-md px-2 py-2 bg-white focus:ring focus:ring-blue-200">--}}
{{--                                <option value="+229">🇧🇯 +229</option>--}}
{{--                                <option value="+226">🇧🇫 +226</option>--}}
{{--                                <option value="+237">🇨🇲 +237</option>--}}
{{--                                <option value="+225">🇨🇮 +225</option>--}}
{{--                                <option value="+243" selected="selected">🇨🇩 +243</option>--}}
{{--                                <option value="+241">🇬🇦 +241</option>--}}
{{--                                <option value="+233">🇬🇭 +233</option>--}}
{{--                                <option value="+254">🇰🇪 +254</option>--}}
{{--                                <option value="+265">🇲🇼 +265</option>--}}
{{--                                <option value="+258">🇲🇿 +258</option>--}}
{{--                                <option value="+234">🇳🇬 +234</option>--}}
{{--                                <option value="+242">🇨🇬 +242</option>--}}
{{--                                <option value="+250">🇷🇼 +250</option>--}}
{{--                                <option value="+221">🇸🇳 +221</option>--}}
{{--                                <option value="+232">🇸🇱 +232</option>--}}
{{--                                <option value="+255">🇹🇿 +255</option>--}}
{{--                                <option value="+256">🇺🇬 +256</option>--}}
{{--                                <option value="+260">🇿🇲 +260</option>--}}
{{--                                <option value="+1">🇺🇸 +1</option>--}}
{{--                            </select>--}}
{{--                            <input type="tel" id="phone" name="phone" required--}}
{{--                                   class="w-2/3 border border-gray-300 rounded-md px-4 py-2 focus:ring focus:ring-blue-200"--}}
{{--                                   placeholder="Enter phone number">--}}
{{--                        </div>--}}
{{--                    </div>--}}

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1" for="phone">Phone Number *</label>
                        <div class="flex gap-2">
                            <select name="country_code" id="country_code" class="select2-country-code w-1/3 border border-gray-300 rounded-md px-2 py-2 bg-white focus:ring focus:ring-blue-200" required>
                                <option value="">Select country</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country['dial_code'] }}"
                                            data-currency="{{ $country['currency'] }}"
                                            data-country-code="{{ $country['alpha3'] }}"
                                            data-flag="{{ strtolower($country['alpha2']) }}"
                                        {{ $country['dial_code'] === "+243" ? 'selected' : '' }}>
                                        {{ $country['dial_code'] }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="tel" id="phone" name="phone" required
                                   class="w-2/3 border border-gray-300 rounded-md px-4 py-2 focus:ring focus:ring-blue-200"
                                   placeholder="Enter phone number">
                        </div>
                    </div>
                    {{-- Email --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1" for="email">Email Address *</label>
                        <input type="email" id="email" name="email" required
                               class="w-full border border-gray-300 rounded-md px-4 py-2 focus:ring focus:ring-blue-200">
                    </div>


                    {{-- Hidden Inputs --}}
                    <input type="hidden" name="price" id="price" value="{{$product->price}}">
                    <input type="hidden" name="product_price" id="product_price" value="{{$product->product_price}}">
                    <input type="hidden" name="product_feeproduct_price" id="product_fee"
                           value="{{$product->product_fee}}">
                    <input type="hidden" name="final_amount" id="final_amount" value="{{$product->price}}">
                    <input type="hidden" name="reference_id" id="reference_id" value="{{$product->reference_id}}">

                    {{-- Submit Button --}}
                    <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white text-lg font-semibold py-3 rounded-md transition">
                        Pay <span id="btn_pay_amount"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Select2 CSS + JS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // const currencyMap = {
        //     'BEN': {code: 'XOF', name: 'West African CFA franc', decimal_supported: false},
        //     'BFA': {code: 'XOF', name: 'West African CFA franc', decimal_supported: false},
        //     'CMR': {code: 'XAF', name: 'Central African CFA franc', decimal_supported: false},
        //     'CIV': {code: 'XOF', name: 'West African CFA franc', decimal_supported: false},
        //     'COD': {code: 'CDF', name: 'Congolese Franc', decimal_supported: false},
        //     'USD': {code: 'USD', name: 'USD', decimal_supported: false},
        //     'GAB': {code: 'XAF', name: 'Central African CFA franc', decimal_supported: false},
        //     'GHA': {code: 'GHS', name: 'Ghanaian Cedi', decimal_supported: false},
        //     'KEN': {code: 'KES', name: 'Kenyan Shilling', decimal_supported: false},
        //     'MWI': {code: 'MWK', name: 'Malawian Kwacha', decimal_supported: false},
        //     'MOZ': {code: 'MZN', name: 'Mozambican Metical', decimal_supported: false},
        //     'NGA': {code: 'NGN', name: 'Nigerian Naira', decimal_supported: false},
        //     'COG': {code: 'XAF', name: 'Central African CFA franc', decimal_supported: false},
        //     'RWA': {code: 'RWF', name: 'Rwandan Franc', decimal_supported: false},
        //     'SEN': {code: 'XOF', name: 'West African CFA franc', decimal_supported: false},
        //     'SLE': {code: 'SLE', name: 'Sierra Leonean Leone', decimal_supported: false},
        //     'TZA': {code: 'TZS', name: 'Tanzanian Shilling', decimal_supported: false},
        //     'UGA': {code: 'UGX', name: 'Ugandan Shilling', decimal_supported: false},
        //     'ZMB': {code: 'ZMW', name: 'Zambian Kwacha', decimal_supported: false}
        // };

        $('#country').on('change', function () {
            let currency = $('option:selected', this).data('currency');
            $('#currency').val(currency);
            let dialCode = $('option:selected', this).data('dial-code');
            $('#country_code').val(dialCode).trigger('change');

            convertUSDToSelectedCurrency();
        });


        function convertUSDToSelectedCurrency() {
            //  const usdAmount = parseFloat($('#product_price').val());
            const targetCurrency = $('#currency').val();

            if (!targetCurrency) return;

            var USD_DOLLAR = 1;

            $.ajax({
                // url: `https://api.exchangerate.host/convert?access_key=d3d453b2a81d2668a138eeac09b7576f&from=USD&to=${targetCurrency}&amount=${USD_DOLLAR}`,
                url: `https://api.exchangerate.host/convert?access_key=e5132a29e4b19e23edfb255ad2e7fc5e&from=USD&to=${targetCurrency}&amount=${USD_DOLLAR}`,
                method: 'GET',
                success: function (response) {
                    if (response.result) {
                        // const finalValue = Math.ceil(parseFloat(response.result.toFixed(2)));
                        currency_rate = response.result.toFixed(6);

                        total_price = Math.ceil(($('#price').val() * currency_rate));
                        $('#final_amount').val(total_price);
                        $('#sp_final_amount_total').text(total_price + " " + targetCurrency);
                        $("#sp_final_amount_total").text(total_price + " " + targetCurrency);

                        product_price = Math.ceil(($('#product_price').val() * currency_rate));
                        $('#sp_product_price').text(product_price + " " + targetCurrency);

                        product_fee = Math.ceil(($('#product_fee').val() * currency_rate));
                        $('#sp_product_fee').text(product_fee + " " + targetCurrency);
                    } else {
                        $('#final_amount').val(0);
                    }
                },
                error: function () {
                    $('#final_amount').val(0);
                }
            });
        }

        function formatCountry(country) {
            if (!country.id) return country.text;

            const flagCode = $(country.element).data('flag');
            const flagUrl = `https://flagcdn.com/h20/${flagCode}.png`;

            return $(`
            <span>
                <img src="${flagUrl}" class="inline-block mr-2" width="20" /> ${country.text}
            </span>
        `);
        }

        $(document).ready(function () {
            $('.select2-country-code').select2({
                templateResult: formatCountry,
                templateSelection: formatCountry,
                minimumResultsForSearch: -1,
                width: '100%'
            });

            $('.select2-country').select2({
                templateResult: formatCountry,
                templateSelection: formatCountry,
                minimumResultsForSearch: -1,
                width: '100%'
            });
        });


        // $('#country, #currency').on('change keyup', function () {
        //     convertUSDToSelectedCurrency();
        // });
        //
        // Optional: convert on page load
        $(document).ready(function () {
          //  $('#country, #currency').trigger('change');
          //   $('#country').val('COD').trigger('change');
          //   $('#currency').val('USD').trigger('change');
          //   $('#country_code').val('+243').trigger('change');
   //         convertUSDToSelectedCurrency();
        });
    </script>
@endsection
