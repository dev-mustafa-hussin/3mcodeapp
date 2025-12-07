<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            العملاء
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="mb-4">قائمة العملاء التجريبية — يمكنك تعديل الحقول كما تريد.</p>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الاسم</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">البريد الإلكتروني</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الهاتف</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($clients as $client)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">{{ $client['id'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">{{ $client['name'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">{{ $client['email'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">{{ $client['phone'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
