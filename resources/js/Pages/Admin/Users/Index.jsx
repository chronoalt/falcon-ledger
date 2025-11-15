import { Head, usePage, useForm, router } from '@inertiajs/react';

const roleOptions = ['admin', 'supervisor', 'pentester', 'client'];

function UserCard({ user, projects, isCurrentUser }) {
    const projectForm = useForm({ project_id: '' });
    const roleForm = useForm({ role: user.roles[0]?.name || '' });

    const handleAddUserToProject = (e) => {
        e.preventDefault();
        projectForm.post(`/admin/users/${user.id}/projects`, {
            onSuccess: () => projectForm.reset(),
        });
    };

    const handleRemoveUserFromProject = (project) => {
        if (!confirm(`Remove user ${user.name} from project ${project.title}?`)) {
            return;
        }
        router.delete(`/admin/users/${user.id}/projects/${project.id}`);
    };

    const handleRoleUpdate = (e) => {
        e.preventDefault();
        roleForm.put(`/admin/users/${user.id}/role`);
    };

    return (
        <div className="rounded-2xl bg-[#d8e4fb] p-4">
            <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                {/* User Info & Role Form */}
                <div className="space-y-4">
                    <div>
                        <h2 className="text-lg font-semibold text-[#f4562c]">{user.name}</h2>
                        <p className="text-sm text-[#03407c]">{user.email}</p>
                    </div>
                    <form onSubmit={handleRoleUpdate} className="flex items-center gap-2">
                        <select
                            name="role"
                            value={roleForm.data.role}
                            onChange={(e) => roleForm.setData('role', e.target.value)}
                            disabled={isCurrentUser}
                            className="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 disabled:bg-gray-200"
                        >
                            {roleOptions.map((role) => (
                                <option key={role} value={role}>
                                    {role.charAt(0).toUpperCase() + role.slice(1)}
                                </option>
                            ))}
                        </select>
                        <button
                            type="submit"
                            disabled={roleForm.processing || isCurrentUser}
                            className="rounded-full bg-amber-500 px-3 py-1.5 text-xs font-semibold text-white shadow hover:bg-amber-600 disabled:opacity-50"
                        >
                            Update Role
                        </button>
                    </form>
                </div>

                {/* Project Assignment Form */}
                <div className="space-y-4">
                    <form onSubmit={handleAddUserToProject} className="flex items-center gap-2">
                        <select
                            name="project_id"
                            value={projectForm.data.project_id}
                            onChange={(e) => projectForm.setData('project_id', e.target.value)}
                            className="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        >
                            <option value="">Assign to project...</option>
                            {projects.map((project) => (
                                <option key={project.id} value={project.id}>{project.title}</option>
                            ))}
                        </select>
                        <button
                            type="submit"
                            className="rounded-full bg-[#004a98] px-4 py-1.5 text-xs font-semibold text-white shadow hover:bg-[#003b77]"
                        >
                            Add
                        </button>
                    </form>
                    <div>
                        <h3 className="text-sm font-semibold text-[#03549a]">Assigned Projects:</h3>
                        {user.projects.length > 0 ? (
                            <ul className="mt-2 list-inside list-disc space-y-1">
                                {user.projects.map((project) => (
                                    <li key={project.id} className="flex items-center justify-between text-sm text-[#03407c]">
                                        <span>{project.title}</span>
                                        <button
                                            onClick={() => handleRemoveUserFromProject(project)}
                                            className="rounded-full border border-rose-400 px-2 py-0.5 text-xs font-semibold text-rose-600 hover:bg-rose-600 hover:text-white"
                                        >
                                            Remove
                                        </button>
                                    </li>
                                ))}
                            </ul>
                        ) : (
                            <p className="mt-1 text-sm text-gray-500">Not assigned to any projects.</p>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
}


export default function Index() {
    const { users, projects, auth, flash } = usePage().props;
    const currentUser = auth.user;

    return (
        <div className="space-y-6">
            <Head title="User Management" />

            <div className="flex items-center justify-between">
                <h1 className="text-3xl font-extrabold text-[#0050a4]">
                    User Management
                </h1>
            </div>

            {flash.success && (
                <div className="rounded-md bg-green-50 p-4 text-sm font-medium text-green-700">
                    {flash.success}
                </div>
            )}
            {flash.error && (
                <div className="rounded-md bg-rose-50 p-4 text-sm font-medium text-rose-700">
                    {flash.error}
                </div>
            )}

            <section className="rounded-2xl bg-[#c7d9f6] p-6 shadow-sm">
                <div className="space-y-6">
                    {users.map((user) => (
                        <UserCard
                            key={user.id}
                            user={user}
                            projects={projects}
                            isCurrentUser={user.id === currentUser.id}
                        />
                    ))}
                </div>
            </section>
        </div>
    );
}
