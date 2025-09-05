@if((@$data['companies'] !="" && Auth::user()->role_id == 1) || env('APP_DEBUG') == true)
<div class="single-select d-none d-lg-block mr-16">
    <input type="hidden" id="change_branch_url" value="{{ route('branchs.ajaxBranchChange') }}">
    <select name="user_branch" class="branch-select" id="change-user-branch">
        @foreach ($branches as $branch)
            <option value="{{ $branch->id }}" {{ $branch->id == getCurrentBranch() ? 'selected' : '' }}>
                {{ $branch->name }}</option>
        @endforeach
    </select>
</div>
@endif
