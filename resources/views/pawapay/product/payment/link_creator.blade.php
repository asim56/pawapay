@extends('layouts.app')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full bg-white p-8 rounded-xl shadow-md">
            <h2 class="text-2xl font-bold mb-6 text-gray-900">Generate Payment Link</h2>

            <div id="form-error" class="text-red-600 text-sm mb-4 hidden"></div>

            <form id="paymentForm">
                @csrf

                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Product Name</label>
                    <input type="text" name="name" id="name" placeholder="e.g mobile"
                           class="w-full border border-gray-300 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <span class="text-red-500 text-sm mt-1 hidden" id="error-name"></span>
                </div>

                <div class="mb-4">
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Price</label>
                    <div class="flex">
                        <input type="number" step=".0001" name="price" id="price" placeholder="e.g 20.00"
                               class="w-full border border-gray-300 rounded-l-md p-3 focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <span
                            class="bg-gray-100 border border-l-0 border-gray-300 rounded-r-md px-4 flex items-center text-gray-700">USD</span>
                    </div>
                    <span class="text-red-500 text-sm mt-1 hidden" id="error-price"></span>
                </div>

                <div class="mb-6">
                    <label for="redirect_url" class="block text-sm font-medium text-gray-700 mb-1">Redirect URL</label>
                    <input type="url" name="redirect_url" id="redirect_url" placeholder="e.g www.redirect.com"
                           class="w-full border border-gray-300 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <span class="text-red-500 text-sm mt-1 hidden" id="error-redirect_url"></span>
                </div>

                <button type="submit" id="btn_submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-md transition">
                    Submit
                </button>
            </form>

            <div id="payment-result" class="mt-6 hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">Payment URL:</label>
                <div
                    class="flex items-center bg-gray-100 border border-gray-300 rounded-md p-2 text-sm text-gray-800 break-all">
                    <span id="payment-url" class="flex-1"></span>
                    <button onclick="copyToClipboard(this)" id="btn_clipboard"
                            class="ml-2 text-gray-600 hover:text-gray-800" title="Copy to clipboard">
                        📋
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.getElementById('paymentForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            // Clear previous errors
            document.querySelectorAll('[id^=error-]').forEach(e => e.textContent = e.classList.add('hidden'));
            document.getElementById('form-error').classList.add('hidden');
            document.getElementById('payment-result').classList.add('hidden');
            const submitBtn = document.getElementById('btn_submit');

            const formData = new FormData(this);
            const csrfToken = document.querySelector('input[name="_token"]').value;

            try {
                const response = await fetch("{{ route('product_payment_create_url') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (!response.ok) {
                    if (response.status === 422 && data.errors) {
                        // Show field-specific validation errors
                        for (const [field, messages] of Object.entries(data.errors)) {
                            const errorSpan = document.getElementById(`error-${field}`);
                            if (errorSpan) {
                                errorSpan.textContent = messages[0];
                                errorSpan.classList.remove('hidden');
                            }
                        }
                    } else if (data.message) {
                        // General error
                        document.getElementById('form-error').textContent = data.message;
                        document.getElementById('form-error').classList.remove('hidden');
                    }
                    return;
                }

                // Success: show the payment URL
                submitBtn.disabled = true;
                submitBtn.setAttribute("class", "w-full bg-gray-500 hover:bg-gray-500 text-white font-semibold py-3 rounded-md transition");
                document.getElementById('payment-url').textContent = data.url;
                document.getElementById('payment-result').classList.remove('hidden');

            } catch (error) {
                submitBtn.disabled = false;
                document.getElementById('form-error').textContent = 'Something went wrong. Please try again.';
                document.getElementById('form-error').classList.remove('hidden');
                console.error(error);
            }
        });

        function copyToClipboard(obj) {
            const text = document.getElementById('payment-url').textContent;
            navigator.clipboard.writeText(text).then(() => {
                const btn = document.getElementById('btn_clipboard');
                btn.textContent = '✅';
                setTimeout(() => btn.textContent = '📋', 2000);
            });
        }
    </script>
@endsection
