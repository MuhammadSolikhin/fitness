<div class="form-group">
    <label for="name">Nama</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name ?? '') }}" required>
</div>

<div class="form-group">
    <label for="email">Email</label>
    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email ?? '') }}" required>
</div>

<div class="form-group">
    <label for="password">Password @if(!empty($user)) <small>(Kosongkan jika tidak ganti)</small> @endif</label>
    <input type="password" name="password" class="form-control">
</div>

<div class="form-group">
    <label for="password_confirmation">Konfirmasi Password</label>
    <input type="password" name="password_confirmation" class="form-control">
</div>

<div class="form-group">
    <label for="role">Role</label>
    <select name="role" class="form-control" required>
        <option value="admin" {{ old('role', $user->role ?? '') == 'admin' ? 'selected' : '' }}>Admin</option>
        <option value="pelatih" {{ old('role', $user->role ?? '') == 'pelatih' ? 'selected' : '' }}>Pelatih</option>
        <option value="user" {{ old('role', $user->role ?? '') == 'user' ? 'selected' : '' }}>User</option>
    </select>
</div>

<div class="form-group">
    <label for="is_membership">Membership Aktif</label>
    <select name="is_membership" class="form-control" required>
        <option value="1" {{ old('is_membership', $user->is_membership ?? '') == true ? 'selected' : '' }}>Ya</option>
        <option value="0" {{ old('is_membership', $user->is_membership ?? '') == false ? 'selected' : '' }}>Tidak</option>
    </select>
</div>

<div class="form-group">
    <label for="membership_expired_at">Tanggal Kadaluarsa Membership</label>
    <input type="date" name="membership_expired_at" class="form-control" value="{{ old('membership_expired_at', isset($user->membership_expired_at) ? $user->membership_expired_at->format('Y-m-d') : '') }}">
</div>
