import { Link } from '@inertiajs/react';

export default function Navigation() {
    return (
        <nav className="flex gap-3 text-sm">
            <Link className="text-slate-700 hover:text-slate-900" href="/projects">
                Projects
            </Link>
        </nav>
    );
}