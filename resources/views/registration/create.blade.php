<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Partisipasi | Bonsai</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-100 text-stone-900">
    <div class="relative overflow-hidden bg-emerald-950">
        <div class="absolute -right-24 -top-32 h-80 w-80 rounded-full bg-amber-300/20 blur-3xl"></div>
        <div class="mx-auto max-w-6xl px-6 py-16 sm:px-8 lg:px-12">
            <p class="mb-4 text-sm font-semibold uppercase tracking-[0.28em] text-amber-300">Bonsai Exhibition</p>
            <h1 class="max-w-3xl font-serif text-4xl font-semibold tracking-tight text-white sm:text-6xl">Daftarkan partisipasi dan bonsai Anda.</h1>
            <p class="mt-6 max-w-2xl text-base leading-7 text-emerald-100 sm:text-lg">Lengkapi data peserta dan semua bonsai dalam satu formulir. Foto asli akan diproses otomatis dengan kualitas tinggi.</p>
        </div>
    </div>

    <main class="mx-auto max-w-6xl px-6 py-10 sm:px-8 lg:px-12">
        @if (session('registration_success'))
            <div class="mb-8 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800" role="status">
                {{ session('registration_success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-8 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-800" role="alert">
                <p class="font-semibold">Periksa kembali data registrasi.</p>
                <ul class="mt-2 list-inside list-disc text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('registration.store') }}" enctype="multipart/form-data" class="space-y-8" id="registration-form">
            @csrf

            <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-stone-200 sm:p-8">
                <div class="mb-7 flex items-end justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-700">01 / Partisipasi</p>
                        <h2 class="mt-2 text-2xl font-semibold">Data peserta</h2>
                    </div>
                    <span class="hidden text-sm text-stone-500 sm:block">Semua kolom wajib diisi</span>
                </div>
                <div class="grid gap-5 md:grid-cols-3">
                    <x-registration-input name="name" label="Nama lengkap" required />
                    <x-registration-input name="email" label="Email" type="email" required />
                    <x-registration-input name="no_hp" label="Nomor WhatsApp" type="tel" required />
                </div>
            </section>

            <section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-stone-200 sm:p-8">
                <div class="mb-7 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-700">02 / Koleksi</p>
                        <h2 class="mt-2 text-2xl font-semibold">Data bonsai</h2>
                        <p class="mt-2 text-sm text-stone-500">Tambahkan satu atau lebih bonsai untuk peserta ini.</p>
                    </div>
                    <button type="button" id="add-bonsai" class="rounded-full bg-emerald-800 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">+ Tambah bonsai</button>
                </div>

                <div id="bonsai-list" class="space-y-6">
                    @php($bonsais = old('bonsais', [[]]))
                    @foreach ($bonsais as $index => $bonsai)
                        @include('registration._bonsai-fields', ['index' => $index, 'bonsai' => $bonsai, 'bonsaiTypes' => $bonsaiTypes])
                    @endforeach
                </div>
            </section>

            <div class="flex flex-col items-center justify-between gap-4 rounded-3xl bg-amber-100 p-6 sm:flex-row sm:p-8">
                <p class="max-w-xl text-sm leading-6 text-amber-950">Pastikan foto menampilkan bonsai dengan jelas. Format yang diterima: JPG, PNG, atau WebP, maksimal 50 MB.</p>
                <button type="submit" class="w-full rounded-full bg-amber-500 px-8 py-4 font-semibold text-amber-950 shadow-sm transition hover:bg-amber-400 sm:w-auto">Kirim registrasi</button>
            </div>
        </form>
    </main>

    <template id="bonsai-template">
        @include('registration._bonsai-fields', ['index' => '__INDEX__', 'bonsai' => [], 'bonsaiTypes' => $bonsaiTypes])
    </template>
    <div id="type-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-stone-950/60 px-6" role="dialog" aria-modal="true" aria-labelledby="type-modal-title">
        <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl sm:p-8">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-700">Jenis baru</p>
                    <h2 id="type-modal-title" class="mt-2 text-2xl font-semibold">Tambah jenis bonsai</h2>
                </div>
                <button type="button" id="close-type-modal" class="text-2xl leading-none text-stone-400 hover:text-stone-700" aria-label="Tutup">&times;</button>
            </div>
            <form id="type-form" class="mt-6 space-y-4">
                <div>
                    <label for="new-type-name" class="mb-2 block text-sm font-medium">Nama jenis bonsai</label>
                    <input id="new-type-name" name="name" required maxlength="255" class="w-full rounded-xl border-stone-300 px-4 py-3 shadow-sm focus:border-emerald-600 focus:ring-emerald-600" placeholder="Contoh: Bonsai Kemuning">
                    <p id="type-error" class="mt-2 hidden text-sm text-red-700"></p>
                </div>
                <button type="submit" class="w-full rounded-full bg-emerald-800 px-5 py-3 font-semibold text-white hover:bg-emerald-700">Simpan jenis bonsai</button>
            </form>
        </div>
    </div>
    <script>
        const list = document.querySelector('#bonsai-list');
        const template = document.querySelector('#bonsai-template');
        const modal = document.querySelector('#type-modal');
        const typeForm = document.querySelector('#type-form');
        const typeName = document.querySelector('#new-type-name');
        const typeError = document.querySelector('#type-error');
        let activeTypeSelect = null;
        let index = list.querySelectorAll('[data-bonsai]').length;

        document.querySelector('#add-bonsai').addEventListener('click', () => {
            list.insertAdjacentHTML('beforeend', template.innerHTML.replaceAll('__INDEX__', index));
            index++;
        });

        list.addEventListener('click', (event) => {
            if (event.target.matches('[data-add-type]')) {
                activeTypeSelect = event.target.closest('[data-type-field]').querySelector('[data-type-select]');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                typeName.focus();
                return;
            }
            if (!event.target.matches('[data-remove-bonsai]')) return;
            const cards = list.querySelectorAll('[data-bonsai]');
            if (cards.length === 1) return;
            event.target.closest('[data-bonsai]').remove();
        });

        const closeTypeModal = () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            typeForm.reset();
            typeError.classList.add('hidden');
        };

        document.querySelector('#close-type-modal').addEventListener('click', closeTypeModal);
        modal.addEventListener('click', (event) => {
            if (event.target === modal) closeTypeModal();
        });

        typeForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            typeError.classList.add('hidden');
            const response = await fetch('{{ route('bonsai-types.store') }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                },
                body: JSON.stringify({ name: typeName.value }),
            });

            if (!response.ok) {
                const data = await response.json();
                typeError.textContent = data.errors?.name?.[0] ?? 'Jenis bonsai gagal disimpan.';
                typeError.classList.remove('hidden');
                return;
            }

            const type = await response.json();
            const option = new Option(type.name, type.id, true, true);
            activeTypeSelect.add(option);
            activeTypeSelect.value = type.id;
            closeTypeModal();
        });
    </script>
</body>
</html>
