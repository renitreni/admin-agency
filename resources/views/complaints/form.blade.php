<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Submit a Complaint – {{ config('app.name') }}</title>
    @vite('resources/css/app.css')
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700" rel="stylesheet" />
    <style>
        body { font-family: 'Figtree', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 antialiased">
    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm shadow-slate-200/50">
            <div class="border-b border-slate-200 px-6 py-5 sm:px-8">
                <h1 class="text-2xl font-semibold text-slate-900">Complaint Form</h1>
                <p class="mt-1 text-sm text-slate-500">Submit your complaint. All fields marked with * are required.</p>
            </div>

            @if(session('success'))
                <div class="mx-6 mt-6 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-800 border border-emerald-200">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mx-6 mt-6 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-800 border border-red-200">
                    <p class="font-medium">Please correct the following errors:</p>
                    <ul class="mt-1 list-inside list-disc">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('complaints.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 px-6 py-6 sm:px-8 sm:py-8">
                @csrf

                {{-- Honeypot: hidden from users, bots often fill it --}}
                <div class="absolute -left-[9999px] top-0" aria-hidden="true">
                    <label for="website">Leave blank</label>
                    <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="foreign_recruitment_agency" class="block text-sm font-medium text-slate-700">Foreign Recruitment Agency *</label>
                        <input type="text" name="foreign_recruitment_agency" id="foreign_recruitment_agency" value="{{ old('foreign_recruitment_agency') }}" required
                            class="mt-1 block w-full rounded-lg border border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="ofw_full_name" class="block text-sm font-medium text-slate-700">OFW's Full Name *</label>
                        <input type="text" name="ofw_full_name" id="ofw_full_name" value="{{ old('ofw_full_name') }}" required
                            class="mt-1 block w-full rounded-lg border border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="gender" class="block text-sm font-medium text-slate-700">Gender *</label>
                        <select name="gender" id="gender" required class="mt-1 block w-full rounded-lg border border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">Select</option>
                            <option value="male" @if(old('gender') == 'male') selected @endif>Male</option>
                            <option value="female" @if(old('gender') == 'female') selected @endif>Female</option>
                            <option value="other" @if(old('gender') == 'other') selected @endif>Other</option>
                        </select>
                    </div>
                    <div>
                        <label for="birthdate" class="block text-sm font-medium text-slate-700">Birthdate *</label>
                        <input type="date" name="birthdate" id="birthdate" value="{{ old('birthdate') }}" required
                            class="mt-1 block w-full rounded-lg border border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="occupation" class="block text-sm font-medium text-slate-700">Occupation *</label>
                        <input type="text" name="occupation" id="occupation" value="{{ old('occupation') }}" required
                            class="mt-1 block w-full rounded-lg border border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="nation_id" class="block text-sm font-medium text-slate-700">Nation ID *</label>
                        <input type="text" name="nation_id" id="nation_id" value="{{ old('nation_id') }}" required
                            class="mt-1 block w-full rounded-lg border border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="passport_no" class="block text-sm font-medium text-slate-700">Passport No. *</label>
                        <input type="text" name="passport_no" id="passport_no" value="{{ old('passport_no') }}" required
                            class="mt-1 block w-full rounded-lg border border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="email" class="block text-sm font-medium text-slate-700">E-mail *</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            class="mt-1 block w-full rounded-lg border border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="contact_person" class="block text-sm font-medium text-slate-700">Contact Person *</label>
                        <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person') }}" required
                            class="mt-1 block w-full rounded-lg border border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="primary_contact" class="block text-sm font-medium text-slate-700">Primary Contact *</label>
                        <input type="text" name="primary_contact" id="primary_contact" value="{{ old('primary_contact') }}" required
                            class="mt-1 block w-full rounded-lg border border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="secondary_contact" class="block text-sm font-medium text-slate-700">Secondary Contact</label>
                        <input type="text" name="secondary_contact" id="secondary_contact" value="{{ old('secondary_contact') }}"
                            class="mt-1 block w-full rounded-lg border border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="address_abroad" class="block text-sm font-medium text-slate-700">Address Abroad *</label>
                        <textarea name="address_abroad" id="address_abroad" rows="3" required
                            class="mt-1 block w-full rounded-lg border border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('address_abroad') }}</textarea>
                    </div>
                    <div class="sm:col-span-2">
                        <label for="complaint" class="block text-sm font-medium text-slate-700">Complaint * <span class="text-slate-400 font-normal">(max 10,000 characters)</span></label>
                        <textarea name="complaint" id="complaint" rows="10" maxlength="10000" required
                            class="mt-1 block w-full rounded-lg border border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('complaint') }}</textarea>
                        <p class="mt-1 text-xs text-slate-500"><span id="complaint-count">0</span> / 10,000 characters</p>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-slate-700">Image Evidences <span class="text-slate-400 font-normal">(optional, max 3 files: JPEG, PNG, GIF, WebP; 5MB each)</span></label>
                        <input type="file" name="image_evidences[]" id="image_evidences" accept="image/jpeg,image/png,image/gif,image/webp" multiple
                            class="mt-1 block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-slate-100 file:px-4 file:py-2 file:text-slate-700 file:font-medium hover:file:bg-slate-200">
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3 border-t border-slate-200 pt-6">
                    <button type="submit" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Submit Complaint
                    </button>
                    <a href="{{ url('/') }}" class="text-sm text-slate-600 hover:text-slate-900">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    @vite('resources/js/app.js')
    <script>
        document.getElementById('complaint').addEventListener('input', function () {
            document.getElementById('complaint-count').textContent = this.value.length;
        });
        document.getElementById('complaint-count').textContent = document.getElementById('complaint').value.length;
    </script>
</body>
</html>
