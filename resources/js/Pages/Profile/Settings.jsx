import { useMemo, useState } from 'react';
import { Head, useForm } from '@inertiajs/react';

export default function Settings({ user }) {
    const initials = useMemo(() => {
        if (!user?.name) return '?';
        return user.name
            .split(' ')
            .filter(Boolean)
            .map((part) => part[0])
            .join('')
            .slice(0, 2)
            .toUpperCase();
    }, [user?.name]);

    const [preview, setPreview] = useState(user?.profile_photo_url || null);

    const { data, setData, post, processing, errors, recentlySuccessful } = useForm({
        name: user?.name || '',
        profile_photo: null,
        _method: 'put',
    });

    const handleSubmit = (event) => {
        event.preventDefault();

        post('/profile', {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                setData('profile_photo', null);
            },
        });
    };

    const handleFileChange = (event) => {
        const file = event.target.files?.[0];
        if (file) {
            setData('profile_photo', file);
            setPreview(URL.createObjectURL(file));
        }
    };

    return (
        <>
            <Head title="Profile Settings" />

            <div className="grid gap-8 lg:grid-cols-3">
                {/* Overview */}
                <div className="lg:col-span-1">
                    <div className="rounded-3xl bg-gradient-to-br from-[#0f172a] via-[#111827] to-[#1f2937] p-6 shadow-2xl">
                        <div className="flex items-center gap-4">
                            {preview ? (
                                <img
                                    src={preview}
                                    alt="Profile"
                                    className="h-16 w-16 rounded-2xl object-cover ring-2 ring-white/30"
                                />
                            ) : (
                                <div className="flex h-16 w-16 items-center justify-center rounded-2xl bg-white/10 text-lg font-semibold text-white ring-2 ring-white/20">
                                    {initials}
                                </div>
                            )}
                            <div>
                                <p className="text-sm uppercase tracking-wider text-orange-200">
                                    Account
                                </p>
                                <h2 className="text-xl font-semibold text-white">{user?.name}</h2>
                                <p className="text-sm text-slate-300">{user?.email}</p>
                            </div>
                        </div>

                        <div className="mt-6 space-y-4">
                            <div>
                                <p className="text-xs uppercase tracking-wide text-orange-200">
                                    Roles
                                </p>
                                <div className="mt-2 flex flex-wrap gap-2">
                                    {user?.roles?.length ? (
                                        user.roles.map((role) => (
                                            <span
                                                key={role}
                                                className="rounded-full bg-orange-500/20 px-3 py-1 text-xs font-semibold text-orange-100 ring-1 ring-orange-400/40"
                                            >
                                                {role}
                                            </span>
                                        ))
                                    ) : (
                                        <span className="text-sm text-slate-300">None</span>
                                    )}
                                </div>
                            </div>

                            <div>
                                <p className="text-xs uppercase tracking-wide text-orange-200">
                                    Accessible projects
                                </p>
                                <div className="mt-2 space-y-2">
                                    {user?.projects?.length ? (
                                        user.projects.map((project) => (
                                            <div
                                                key={project.id}
                                                className="rounded-xl border border-white/5 bg-white/5 px-3 py-2 text-sm text-slate-100"
                                            >
                                                {project.title}
                                            </div>
                                        ))
                                    ) : (
                                        <p className="text-sm text-slate-300">
                                            No assigned projects yet.
                                        </p>
                                    )}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Form */}
                <div className="lg:col-span-2">
                    <div className="rounded-3xl border border-orange-100/50 bg-white p-8 shadow-xl">
                        <div className="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                            <div>
                                <p className="text-xs uppercase tracking-wide text-slate-500">
                                    Profile Settings
                                </p>
                                <h2 className="text-2xl font-semibold text-[#0f172a]">
                                    Update your details
                                </h2>
                                <p className="text-sm text-slate-500">
                                    Change your display name or refresh your avatar. Images are
                                    validated for size and type.
                                </p>
                            </div>
                            {recentlySuccessful && (
                                <span className="rounded-full bg-emerald-100 px-3 py-1 text-sm font-semibold text-emerald-700">
                                    Saved
                                </span>
                            )}
                        </div>

                        <form onSubmit={handleSubmit} className="mt-6 space-y-6">
                            <div className="space-y-2">
                                <label
                                    htmlFor="name"
                                    className="text-sm font-semibold text-slate-800"
                                >
                                    Username
                                </label>
                                <input
                                    id="name"
                                    type="text"
                                    value={data.name}
                                    onChange={(e) => setData('name', e.target.value)}
                                    className="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-[#f4562c] focus:outline-none focus:ring-2 focus:ring-[#f4562c]/30"
                                    placeholder="Your display name"
                                    required
                                />
                                {errors.name && (
                                    <p className="text-xs text-rose-600">{errors.name}</p>
                                )}
                            </div>

                            <div className="space-y-2">
                                <label
                                    htmlFor="profile_photo"
                                    className="text-sm font-semibold text-slate-800"
                                >
                                    Profile picture
                                </label>
                                <div className="flex items-center gap-4">
                                    {preview ? (
                                        <img
                                            src={preview}
                                            alt="Preview"
                                            className="h-14 w-14 rounded-2xl object-cover ring-2 ring-orange-200"
                                        />
                                    ) : (
                                        <div className="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-sm font-semibold text-slate-500 ring-2 ring-slate-200">
                                            {initials}
                                        </div>
                                    )}

                                    <label className="inline-flex cursor-pointer items-center gap-2 rounded-full bg-[#004a98] px-4 py-2 text-sm font-semibold text-white shadow hover:bg-[#003b77]">
                                        <input
                                            id="profile_photo"
                                            type="file"
                                            accept="image/png,image/jpeg,image/jpg,image/webp"
                                            className="hidden"
                                            onChange={handleFileChange}
                                        />
                                        Upload new photo
                                    </label>
                                </div>
                                <p className="text-xs text-slate-500">
                                    PNG, JPG, or WEBP. Max size 2 MB.
                                </p>
                                {errors.profile_photo && (
                                    <p className="text-xs text-rose-600">{errors.profile_photo}</p>
                                )}
                            </div>

                            <div className="pt-2">
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="inline-flex items-center gap-2 rounded-full bg-[#f4562c] px-6 py-3 text-sm font-semibold text-white shadow-md transition hover:bg-[#d44520] disabled:opacity-60"
                                >
                                    {processing ? 'Saving…' : 'Save changes'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </>
    );
}
