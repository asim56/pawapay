@extends('admin.layouts.app')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-purple-50">
        <div class="bg-white shadow-xl rounded-2xl p-8 w-full max-w-lg">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Payment Checkout</h2>

            <form action="{{ url('/pay') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                    <input type="text" name="phoneNumber" placeholder="e.g. 260763456789" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-purple-600 focus:border-purple-600" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Customer Email</label>
                    <input type="email" name="customerId" placeholder="customer@email.com" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-purple-600 focus:border-purple-600" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Amount</label>
                        <input type="number" name="amount" placeholder="e.g. 15" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-purple-600 focus:border-purple-600" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Currency</label>
                        <select name="currency" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-purple-600 focus:border-purple-600" required>
                            <option value="ZMW">ZMW</option>
                            <option value="KES">KES</option>
                            <option value="UGX">UGX</option>
                            <option value="GHS">GHS</option>
                            <option value="RWF">RWF</option>
                            <!-- Add more currencies as needed -->
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Country</label>
                    <select name="country" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-purple-600 focus:border-purple-600" required>
                        <option value="ZMB">Benin</option>
                        <option value="ZMB">Burkina Faso</option>
                        <option value="KEN">Cameroon</option>
                        <option value="UGA">Côte d'Ivoire</option>
                        <option value="GHA">Democratic Republic of the congo (DRC)</option>
                        <option value="RWA">Rwanda</option>
                        <!-- Add more countries if supported -->
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Reason</label>
                    <input type="text" name="reason" placeholder="e.g. reason" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-purple-600 focus:border-purple-600" required>
                </div>
                <div class="pt-4 flex justify-between">
                    <button type="reset" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Cancel</button>
                    <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">Pay</button>
                </div>
            </form>

            <div class="mt-4 text-center text-sm text-gray-500">
                 <span class="font-medium text-purple-600">pawaPay</span>
            </div>
        </div>
    </div>
@endsection
