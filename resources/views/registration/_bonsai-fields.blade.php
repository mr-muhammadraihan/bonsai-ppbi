@php
    $value = fn (string $field, mixed $default = '') => old("bonsais.{$index}.{$field}", $bonsai[$field] ?? $default);
@endphp
<article data-bonsai class="relative rounded-2xl border border-stone-200 bg-stone-50 p-5 sm:p-6">
    <div class="mb-5 flex items-center justify-between gap-4">
        <h3 class="font-semibold">Bonsai <span data-bonsai-number>{{ is_numeric($index) ? $index + 1 : '' }}</span></h3>
        <button type="button" data-remove-bonsai class="text-sm font-medium text-red-700 hover:text-red-900">Hapus</button>
    </div>
    <div class="grid gap-5 md:grid-cols-2">
        <x-registration-input name="bonsai_type" label="Jenis bonsai" :index="$index" :value="$value('bonsai_type')" required />
        <div>
            <label for="bonsais-{{ $index }}-size" class="mb-2 block text-sm font-medium">Ukuran</label>
            <select id="bonsais-{{ $index }}-size" name="bonsais[{{ $index }}][size]" required class="w-full rounded-xl border-stone-300 bg-white px-4 py-3 shadow-sm focus:border-emerald-600 focus:ring-emerald-600">
                <option value="">Pilih ukuran</option>
                @foreach (['Small', 'Medium', 'Large', 'Mame'] as $size)<option value="{{ $size }}" @selected($value('size') === $size)>{{ $size }}</option>@endforeach
            </select>
        </div>
        <div>
            <label for="bonsais-{{ $index }}-class" class="mb-2 block text-sm font-medium">Kelas</label>
            <select id="bonsais-{{ $index }}-class" name="bonsais[{{ $index }}][class]" required class="w-full rounded-xl border-stone-300 bg-white px-4 py-3 shadow-sm focus:border-emerald-600 focus:ring-emerald-600">
                @foreach (['Jadi', 'Prospek'] as $class)<option value="{{ $class }}" @selected($value('class', 'Jadi') === $class)>{{ $class }}</option>@endforeach
            </select>
        </div>
        <div>
            <label for="bonsais-{{ $index }}-status" class="mb-2 block text-sm font-medium">Status</label>
            <select id="bonsais-{{ $index }}-status" name="bonsais[{{ $index }}][status]" required class="w-full rounded-xl border-stone-300 bg-white px-4 py-3 shadow-sm focus:border-emerald-600 focus:ring-emerald-600">
                @foreach (['Peserta', 'Pemenang'] as $status)<option value="{{ $status }}" @selected($value('status', 'Peserta') === $status)>{{ $status }}</option>@endforeach
            </select>
        </div>
        <x-registration-input name="predicate" label="Predikat (opsional)" :index="$index" :value="$value('predicate')" />
        <div class="md:col-span-2">
            <label for="bonsais-{{ $index }}-description" class="mb-2 block text-sm font-medium">Deskripsi (opsional)</label>
            <textarea id="bonsais-{{ $index }}-description" name="bonsais[{{ $index }}][description]" rows="3" class="w-full rounded-xl border-stone-300 bg-white px-4 py-3 shadow-sm focus:border-emerald-600 focus:ring-emerald-600">{{ $value('description') }}</textarea>
        </div>
        <div class="md:col-span-2">
            <label for="bonsais-{{ $index }}-photo" class="mb-2 block text-sm font-medium">Foto bonsai</label>
            <input id="bonsais-{{ $index }}-photo" name="bonsais[{{ $index }}][photo]" type="file" accept="image/jpeg,image/png,image/webp" capture="environment" required class="block w-full rounded-xl border border-stone-300 bg-white text-sm file:mr-4 file:border-0 file:bg-emerald-800 file:px-4 file:py-3 file:font-semibold file:text-white hover:file:bg-emerald-700">
            @error("bonsais.{$index}.photo")<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
        </div>
    </div>
</article>
