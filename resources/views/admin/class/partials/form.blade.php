<div class="form-group">
    <label for="name">Nama Kelas</label>
    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
        value="{{ old('name', isset($class) ? $class->name : '') }}" required>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="sanggar_name">Nama Sanggar</label>
    <select name="sanggar_name" id="sanggar_name" class="form-control @error('sanggar_name') is-invalid @enderror" required>
        <option value="sanggar-1" {{ old('sanggar_name', isset($class) ? $class->sanggar_name : '') == 'sanggar-1' ? 'selected' : '' }}>
            Sanggar Kurnia 1
        </option>
        <option value="sanggar-2" {{ old('sanggar_name', isset($class) ? $class->sanggar_name : '') == 'sanggar-2' ? 'selected' : '' }}>
            Sanggar Kurnia 2
        </option>
        <option value="sanggar-3" {{ old('sanggar_name', isset($class) ? $class->sanggar_name : '') == 'sanggar-3' ? 'selected' : '' }}>
            Sanggar Kurnia 3
        </option>
    </select>
    @error('sanggar_name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="image">Gambar Kelas</label>
    <input type="file" name="image" id="image" class="form-control-file @error('image') is-invalid @enderror">
    @if(isset($class) && $class->image_path)
        <div class="mt-2">
            <small class="text-muted">Gambar saat ini:</small><br>
            <img src="{{ asset('storage/' . $class->image_path) }}" width="100" class="img-thumbnail mt-1">
        </div>
    @endif
    @error('image')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="description">Deskripsi</label>
    <textarea name="description" id="description"
        class="form-control @error('description') is-invalid @enderror">{{ old('description', isset($class) ? $class->description : '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group">
    <label for="coach_id">Pelatih</label>
    <select name="coach_id" id="coach_id" class="form-control @error('coach_id') is-invalid @enderror" required>
        <option value="">-- Pilih Pelatih --</option>
        @foreach ($coaches as $coach)
            <option value="{{ $coach->id }}" {{ old('coach_id', isset($class) ? $class->coach_id : '') == $coach->id ? 'selected' : '' }}>
                {{ $coach->name }}
            </option>
        @endforeach
    </select>
    @error('coach_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>