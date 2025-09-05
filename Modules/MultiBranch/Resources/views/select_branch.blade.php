@if (!isMainCompany() && config('app.branch') === 'MultiBranch' && isModuleActive('MultiBranch'))
    @php
        $branches = config('app.single_db') ? \App\Models\Branch::authorizable()->where('status_id', 1)->get() : \App\Models\Branch::where('status_id', 1)->get();
    @endphp
    <div class="single-select d-none d-lg-block mr-16">
        <input type="hidden" id="change_branch_url" value="{{ route('branches.ajaxBranchChange') }}">
        <select name="user_branch" class="branch-select" id="change-user-branch">

            @foreach ($branches as $branch)
                <option value="{{ $branch->id }}" {{ $branch->id == userBranch() ? 'selected' : '' }}>
                    {{ $branch->name }}</option>
            @endforeach
        </select>
    </div>
@endif
