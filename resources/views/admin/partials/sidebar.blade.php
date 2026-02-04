<div class="sidebar">
    <ul class="list-group">
        <li class="list-group-item">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
        </li>

        <li class="list-group-item">
            <a href="{{ route('admin.payments') }}">Payments</a>
        </li>

        <li class="list-group-item">
            <a href="{{ route('admin.students') }}">Students</a>
        </li>

        <li class="list-group-item">
            <a href="{{ route('admin.reports') }}">Reports</a>
        </li>

        <!-- Logout -->
        <li class="list-group-item">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                        class="btn btn-link text-danger p-0"
                        onclick="return confirm('Are you sure you want to logout?')">
                    Logout
                </button>
            </form>
        </li>
    </ul>
</div>
