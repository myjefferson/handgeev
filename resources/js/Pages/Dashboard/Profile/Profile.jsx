import React, { useState, useEffect } from 'react';
import { Head, usePage, useForm, router } from '@inertiajs/react';
import useLang from '@/Hooks/useLang';
import DashboardLayout from '@/Layouts/DashboardLayout';
import Alerts from '@/Components/Alerts/Alert';
import ModalChangeEmail from '@/Components/Modals/ChangeEmailModal';
import ModalDeleteAccount from '@/Components/Modals/DeleteAccountModal';

export default function Profile({ userStats }){
    const { auth } = usePage().props;
    const { __ } = useLang();
    const [activeTab, setActiveTab] = useState('profile');
    const [showEmailModal, setShowEmailModal] = useState(false);
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [isLoading, setIsLoading] = useState(false);

    // Forms
    const profileForm = useForm({
        name: auth.user.name,
        surname: auth.user.surname,
        phone: auth.user.phone,
    });

    const passwordForm = useForm({
        current_password: '',
        new_password: '',
        new_password_confirmation: '',
    });

    const emailForm = useForm({
        email: '',
        current_password: '',
    });

    const deleteForm = useForm({
        password: '',
    });

    // Effects
    useEffect(() => {
        // Initialize phone mask
        if (window.$ && window.$.fn.mask) {
            window.$('#phone').mask('(00) 00000-0000');
        }
    }, []);

    // Handlers
    const handleProfileSubmit = (e) => {
        e.preventDefault();
        setIsLoading(true);
        profileForm.put(route('user.profile.update'), {
            onFinish: () => setIsLoading(false),
        });
    };

    const handlePasswordSubmit = (e) => {
        e.preventDefault();
        setIsLoading(true);
        passwordForm.put(route('user.profile.password.update'), {
            onFinish: () => {
                setIsLoading(false);
                passwordForm.reset();
            },
        });
    };

    const handleEmailSubmit = (e) => {
        e.preventDefault();
        setIsLoading(true);
        emailForm.put(route('email.update'), {
            onFinish: () => {
                setIsLoading(false);
                if (!emailForm.hasErrors) {
                    setShowEmailModal(false);
                    emailForm.reset();
                }
            },
        });
    };

    const handleDeleteSubmit = (e) => {
        e.preventDefault();
        setIsLoading(true);
        deleteForm.delete(route('user.account.delete'), {
            onFinish: () => setIsLoading(false),
        });
    };

    const handleResendEmailConfirmation = () => {
        setIsLoading(true);
        router.put(route('email.update'), { email_confirm: true }, {
            onFinish: () => setIsLoading(false),
        });
    };

    const resetProfileForm = () => {
        profileForm.setData({
            name: auth.user.name,
            surname: auth.user.surname,
            phone: auth.user.phone,
        });
    };

    const resetDeleteForm = () => {
        deleteForm.reset();
    };

    // Helper functions
    const getBadgeType = () => {
        if (auth.user.plan?.name === 'admin') return 'admin';
        if (auth.user.plan?.name === 'premium') return 'premium';
        if (auth.user.plan?.name === 'pro') return 'pro';
        if (auth.user.plan?.name === 'start') return 'start';
        return 'free';
    };

    const getBadgeConfig = (type) => {
        const configs = {
            admin: { bg: 'bg-blue-500/20', text: 'text-blue-400', border: 'border-blue-400/30', icon: 'fa-shield-alt', label: __('sidebar.badges.admin')},
            premium: { bg: 'bg-orange-500/20', text: 'text-orange-400', border: 'border-orange-400/30', icon: 'fa-crown', label: __('sidebar.badges.premium')},
            pro: { bg: 'bg-purple-500/20', text: 'text-purple-400', border: 'border-purple-400/30', icon: 'fa-crown', label: __('sidebar.badges.pro')},
            start: { bg: 'bg-teal-500/20', text: 'text-teal-400', border: 'border-teal-400/30', icon: 'fa-crown', label: __('sidebar.badges.start')},
            free: { bg: 'bg-teal-500/20', text: 'text-teal-400', border: 'border-teal-400/30', icon: 'fa-user', label: __('sidebar.badges.free')},
        };
        return configs[type] || configs.free;
    };

    const badgeConfig = getBadgeConfig(getBadgeType());

    return (
        <DashboardLayout>
            <Head title={__('title')} description={__('description')} />

            <div className="min-h-screen bg-slate-900 py-8">
                <div className="max-w-7xl mx-auto p-0 sm:p-0 md:p-6">
                    {/* Header */}
                    <div className="mb-8">
                        <h1 className="text-3xl font-bold text-white mb-2">
                            {__('header.title')}
                        </h1>
                        <p className="text-gray-400">{__('header.subtitle')}</p>
                        <div className="w-20 h-1 bg-teal-400 rounded-full mt-3"></div>
                    </div>

                    {/* Alerts */}
                    <Alerts />

                    <div className="grid grid-cols-1 lg:grid-cols-4 gap-6">
                        {/* Sidebar - User Info */}
                        <div className="lg:col-span-1">
                            <div className="bg-slate-800 rounded-2xl shadow-lg p-6 border border-slate-700">
                                {/* Avatar */}
                                <div className="text-center mb-6">
                                    <div className="w-24 h-24 bg-teal-400/10 rounded-full flex items-center justify-center mx-auto mb-4 border-2 border-teal-400/30">
                                        <i className="fas fa-user text-teal-400 text-3xl"></i>
                                    </div>
                                    <h2 className="text-lg font-bold text-white">
                                        {auth.user.name} {auth.user.surname}
                                    </h2>
                                    <p className="text-gray-400 text-sm">{auth.user.email}</p>
                                    
                                    {/* Profile Type Badge */}
                                    <div className="mt-3">
                                        <span className={`inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ${badgeConfig.bg} ${badgeConfig.text} border ${badgeConfig.border}`}>
                                            <i className={`fas ${badgeConfig.icon} mr-1`}></i>
                                            {badgeConfig.label}
                                        </span>
                                    </div>

                                    {/* Upgrade Button */}
                                    {getBadgeType() === 'free' && (
                                        <div className="mt-4 border-slate-700">
                                            <a
                                                href={route('subscription.pricing')}
                                                className="w-full bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white text-sm font-medium py-2 px-4 rounded-xl transition-all duration-300 flex items-center justify-center"
                                            >
                                                <i className={`fas ${__('sidebar.upgrade.icon')} mr-2`}></i>
                                                {__('sidebar.upgrade.button')}
                                            </a>
                                        </div>
                                    )}
                                </div>

                                {/* Quick Stats */}
                                <div className="mt-6 pt-6 border-t border-slate-700">
                                    <h3 className="text-sm font-medium text-gray-400 mb-3">
                                        {__('sidebar.stats.title')}
                                    </h3>
                                    <div className="space-y-2 text-sm">
                                        <div className="flex justify-between items-center">
                                            <span className="text-gray-400">
                                                {__('sidebar.stats.workspaces')}
                                            </span>
                                            <span className="text-white font-medium">{userStats.workspaces}</span>
                                        </div>
                                        <div className="flex justify-between items-center">
                                            <span className="text-gray-400">
                                                {__('sidebar.stats.topics')}
                                            </span>
                                            <span className="text-white font-medium">{userStats.topics}</span>
                                        </div>
                                        <div className="flex justify-between items-center">
                                            <span className="text-gray-400">
                                                {__('sidebar.stats.fields')}
                                            </span>
                                            <span className="text-white font-medium">{userStats.fields}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Main Content - Tabs */}
                        <div className="lg:col-span-3">
                            <div className="bg-slate-800 rounded-2xl shadow-lg border border-slate-700">
                                {/* Tabs */}
                                <div className="border-b border-slate-700">
                                    <ul className="flex flex-col sm:flex-row -mb-px text-sm font-medium text-center">
                                        <li className="me-2 w-full" role="presentation">
                                            <button
                                                className={`inline-block p-4 border-b-2 rounded-t-lg w-full ${
                                                    activeTab === 'profile'
                                                        ? 'text-teal-400 border-teal-400'
                                                        : 'text-gray-400 border-transparent hover:text-gray-300 hover:border-gray-300'
                                                }`}
                                                onClick={() => setActiveTab('profile')}
                                            >
                                                <i className={`fas ${__('tabs.personal_info.icon')} mr-2`}></i>
                                                {__('tabs.personal_info.label')}
                                            </button>
                                        </li>
                                        <li className="me-2 w-full" role="presentation">
                                            <button
                                                className={`inline-block p-4 border-b-2 rounded-t-lg w-full ${
                                                    activeTab === 'password'
                                                        ? 'text-teal-400 border-teal-400'
                                                        : 'text-gray-400 border-transparent hover:text-gray-300 hover:border-gray-300'
                                                }`}
                                                onClick={() => setActiveTab('password')}
                                            >
                                                <i className={`fas ${__('tabs.password.icon')} mr-2`}></i>
                                                {__('tabs.password.label')}
                                            </button>
                                        </li>
                                        <li className="me-2 w-full" role="presentation">
                                            <button
                                                className={`inline-block p-4 border-b-2 rounded-t-lg w-full ${
                                                    activeTab === 'account'
                                                        ? 'text-teal-400 border-teal-400'
                                                        : 'text-gray-400 border-transparent hover:text-gray-300 hover:border-gray-300'
                                                }`}
                                                onClick={() => setActiveTab('account')}
                                            >
                                                <i className="fas fa-cog mr-2"></i>
                                                Configurações da Conta
                                            </button>
                                        </li>
                                    </ul>
                                </div>

                                {/* Tab Content */}
                                <div>
                                    {/* Personal Info Tab */}
                                    {activeTab === 'profile' && (
                                        <div className="p-6 rounded-b-2xl flex justify-center tab-content">
                                            <div className="max-w-3xl">
                                                <h2 className="text-xl font-semibold text-white mb-6">
                                                    {__('tabs.personal_info.title')}
                                                </h2>

                                                <form onSubmit={handleProfileSubmit}>
                                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                                        {/* Name */}
                                                        <div>
                                                            <label htmlFor="name" className="block text-sm font-medium text-gray-400 mb-2">
                                                                {__('forms.personal_info.name.label')}
                                                            </label>
                                                            <input
                                                                type="text"
                                                                id="name"
                                                                value={profileForm.data.name}
                                                                onChange={(e) => profileForm.setData('name', e.target.value)}
                                                                className={`w-full bg-slate-700 border ${
                                                                    profileForm.errors.name ? 'border-red-500' : 'border-slate-600'
                                                                } rounded-xl py-3 px-4 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors`}
                                                                placeholder={__('forms.personal_info.name.placeholder')}
                                                            />
                                                            {profileForm.errors.name && (
                                                                <span className="text-red-400 text-sm mt-1">{profileForm.errors.name}</span>
                                                            )}
                                                        </div>

                                                        {/* Surname */}
                                                        <div>
                                                            <label htmlFor="surname" className="block text-sm font-medium text-gray-400 mb-2">
                                                                {__('forms.personal_info.surname.label')}
                                                            </label>
                                                            <input
                                                                type="text"
                                                                id="surname"
                                                                value={profileForm.data.surname}
                                                                onChange={(e) => profileForm.setData('surname', e.target.value)}
                                                                className={`w-full bg-slate-700 border ${
                                                                    profileForm.errors.surname ? 'border-red-500' : 'border-slate-600'
                                                                } rounded-xl py-3 px-4 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors`}
                                                                placeholder={__('forms.personal_info.surname.placeholder')}
                                                            />
                                                            {profileForm.errors.surname && (
                                                                <span className="text-red-400 text-sm mt-1">{profileForm.errors.surname}</span>
                                                            )}
                                                        </div>
                                                    </div>

                                                    {/* Email */}
                                                    <div className="mb-6">
                                                        <label htmlFor="email" className="block text-sm font-medium text-gray-400 mb-2">
                                                            {__('forms.personal_info.email.label')}
                                                        </label>
                                                        
                                                        <div className="flex space-x-3">
                                                            <div className="flex-1">
                                                                <input
                                                                    type="email"
                                                                    id="email"
                                                                    value={auth.user.email}
                                                                    className="w-full bg-slate-700 border border-slate-600 rounded-xl py-3 px-4 text-slate-400 placeholder-gray-500 outline-transparent focus:border-transparent transition-colors"
                                                                    placeholder={__('forms.personal_info.email.placeholder')}
                                                                    readOnly
                                                                />
                                                            </div>
                                                            
                                                            <button
                                                                type="button"
                                                                onClick={() => setShowEmailModal(true)}
                                                                className="flex-shrink-0 px-4 py-3 bg-teal-600 hover:bg-teal-500 text-white rounded-xl transition-colors duration-200 flex items-center space-x-2"
                                                            >
                                                                <i className="fas fa-edit"></i>
                                                                <span>Alterar</span>
                                                            </button>
                                                        </div>
                                                        
                                                        <div className="mt-3 flex items-center justify-between">
                                                            <div className="flex items-center space-x-2">
                                                                {auth.user.email_verified_at ? (
                                                                    <div className="flex items-center text-green-400 text-sm">
                                                                        <i className="fas fa-check-circle mr-1"></i>
                                                                        <span>Email confirmado</span>
                                                                    </div>
                                                                ) : (
                                                                    <div className="flex items-center text-amber-400 text-sm">
                                                                        <i className="fas fa-exclamation-triangle mr-1"></i>
                                                                        <span>Email não confirmado</span>
                                                                    </div>
                                                                )}
                                                            </div>
                                                            
                                                            {!auth.user.email_verified_at && (
                                                                <div className="inline">
                                                                    <button
                                                                        type="button"
                                                                        onClick={handleResendEmailConfirmation}
                                                                        disabled={isLoading}
                                                                        className="email_confirm text-cyan-400 hover:text-cyan-300 text-sm flex items-center space-x-1 transition-colors duration-200"
                                                                    >
                                                                        <i className="fas fa-paper-plane mr-1"></i>
                                                                        <span>
                                                                            {isLoading ? 'Enviando...' : 'Reenviar confirmação'}
                                                                        </span>
                                                                    </button>
                                                                </div>
                                                            )}
                                                        </div>
                                                        
                                                        {profileForm.errors.email && (
                                                            <span className="text-red-400 text-sm mt-1">{profileForm.errors.email}</span>
                                                        )}
                                                    </div>

                                                    {/* Phone */}
                                                    <div className="mb-6">
                                                        <label htmlFor="phone" className="block text-sm font-medium text-gray-400 mb-2">
                                                            {__('forms.personal_info.phone.label')}
                                                        </label>
                                                        <input
                                                            type="tel"
                                                            id="phone"
                                                            value={profileForm.data.phone}
                                                            onChange={(e) => profileForm.setData('phone', e.target.value)}
                                                            className={`w-full bg-slate-700 border ${
                                                                profileForm.errors.phone ? 'border-red-500' : 'border-slate-600'
                                                            } rounded-xl py-3 px-4 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors`}
                                                            placeholder={__('forms.personal_info.phone.placeholder')}
                                                        />
                                                        {profileForm.errors.phone && (
                                                            <span className="text-red-400 text-sm mt-1">{profileForm.errors.phone}</span>
                                                        )}
                                                    </div>

                                                    {/* Buttons */}
                                                    <div className="flex flex-col sm:flex-row gap-4 pt-4">
                                                        <button
                                                            type="submit"
                                                            disabled={isLoading || profileForm.processing}
                                                            className="flex-1 bg-teal-500 hover:bg-teal-400 text-slate-900 font-medium py-3 px-6 rounded-xl transition-colors duration-300 teal-glow-hover flex items-center justify-center disabled:opacity-50"
                                                        >
                                                            <i className={`fas ${__('forms.personal_info.buttons.icons.save')} mr-2`}></i>
                                                            {isLoading ? __('processing.text') : __('forms.personal_info.buttons.save')}
                                                        </button>

                                                        <button
                                                            type="button"
                                                            onClick={resetProfileForm}
                                                            className="flex-1 bg-slate-700 hover:bg-slate-600 text-white font-medium py-3 px-6 rounded-xl transition-colors duration-300 border border-slate-600 flex items-center justify-center"
                                                        >
                                                            <i className={`fas ${__('forms.personal_info.buttons.icons.cancel')} mr-2`}></i>
                                                            {__('forms.personal_info.buttons.cancel')}
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    )}

                                    {/* Password Tab */}
                                    {activeTab === 'password' && (
                                        <div className="p-6 rounded-b-2xl flex justify-center tab-content">
                                            <div className="max-w-2xl">
                                                <h2 className="text-xl font-semibold text-white mb-6">
                                                    {__('tabs.password.title')}
                                                </h2>

                                                <form onSubmit={handlePasswordSubmit}>
                                                    {/* Current Password */}
                                                    <div className="mb-6">
                                                        <label htmlFor="current_password" className="block text-sm font-medium text-gray-400 mb-2">
                                                            {__('forms.password.current_password.label')}
                                                        </label>
                                                        <input
                                                            type="password"
                                                            id="current_password"
                                                            value={passwordForm.data.current_password}
                                                            onChange={(e) => passwordForm.setData('current_password', e.target.value)}
                                                            className={`w-full bg-slate-700 border ${
                                                                passwordForm.errors.current_password ? 'border-red-500' : 'border-slate-600'
                                                            } rounded-xl py-3 px-4 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors`}
                                                            placeholder={__('forms.password.current_password.placeholder')}
                                                        />
                                                        {passwordForm.errors.current_password && (
                                                            <span className="text-red-400 text-sm mt-1">{passwordForm.errors.current_password}</span>
                                                        )}
                                                    </div>

                                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                                        {/* New Password */}
                                                        <div>
                                                            <label htmlFor="new_password" className="block text-sm font-medium text-gray-400 mb-2">
                                                                {__('forms.password.new_password.label')}
                                                            </label>
                                                            <input
                                                                type="password"
                                                                id="new_password"
                                                                value={passwordForm.data.new_password}
                                                                onChange={(e) => passwordForm.setData('new_password', e.target.value)}
                                                                className={`w-full bg-slate-700 border ${
                                                                    passwordForm.errors.new_password ? 'border-red-500' : 'border-slate-600'
                                                                } rounded-xl py-3 px-4 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors`}
                                                                placeholder={__('forms.password.new_password.placeholder')}
                                                            />
                                                            {passwordForm.errors.new_password && (
                                                                <span className="text-red-400 text-sm mt-1">{passwordForm.errors.new_password}</span>
                                                            )}
                                                        </div>

                                                        {/* Confirm New Password */}
                                                        <div>
                                                            <label htmlFor="new_password_confirmation" className="block text-sm font-medium text-gray-400 mb-2">
                                                                {__('forms.password.confirm_password.label')}
                                                            </label>
                                                            <input
                                                                type="password"
                                                                id="new_password_confirmation"
                                                                value={passwordForm.data.new_password_confirmation}
                                                                onChange={(e) => passwordForm.setData('new_password_confirmation', e.target.value)}
                                                                className="w-full bg-slate-700 border border-slate-600 rounded-xl py-3 px-4 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors"
                                                                placeholder={__('forms.password.confirm_password.placeholder')}
                                                            />
                                                        </div>
                                                    </div>

                                                    {/* Security Tips */}
                                                    <div className="bg-slate-700/50 rounded-xl p-4 mb-6">
                                                        <h4 className="text-sm font-medium text-teal-400 mb-2">
                                                            <i className={`fas ${__('forms.password.tips.icon')} mr-2`}></i>
                                                            {__('forms.password.tips.title')}
                                                        </h4>
                                                        <ul className="text-xs text-gray-400 space-y-1">
                                                            {Object.values(__('forms.password.tips.items')).map((tip, index) => (
                                                                <li key={index}>• {tip}</li>
                                                            ))}
                                                        </ul>
                                                    </div>

                                                    {/* Buttons */}
                                                    <div className="flex flex-col sm:flex-row gap-4 pt-4">
                                                        <button
                                                            type="submit"
                                                            disabled={isLoading || passwordForm.processing}
                                                            className="flex-1 bg-teal-500 hover:bg-teal-400 text-slate-900 font-medium py-3 px-6 rounded-xl transition-colors duration-300 teal-glow-hover flex items-center justify-center disabled:opacity-50"
                                                        >
                                                            <i className={`fas ${__('forms.password.buttons.icons.update')} mr-2`}></i>
                                                            {isLoading ? __('processing.text') : __('forms.password.buttons.update')}
                                                        </button>

                                                        <button
                                                            type="button"
                                                            onClick={() => setActiveTab('profile')}
                                                            className="flex-1 bg-slate-700 hover:bg-slate-600 text-white font-medium py-3 px-6 rounded-xl transition-colors duration-300 border border-slate-600 flex items-center justify-center"
                                                        >
                                                            <i className={`fas ${__('forms.password.buttons.icons.back')} mr-2`}></i>
                                                            {__('forms.password.buttons.back')}
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    )}

                                    {/* Account Settings Tab */}
                                    {activeTab === 'account' && (
                                        <div className="p-6 rounded-b-2xl flex justify-center tab-content">
                                            <div className="max-w-2xl w-full">
                                                <h2 className="text-xl font-semibold text-white mb-6">
                                                    Configurações da Conta
                                                </h2>

                                                {/* Delete Account Section */}
                                                <div className="bg-slate-700/50 rounded-xl p-6 border border-red-500/20">
                                                    <div className="flex items-start space-x-4">
                                                        <div className="flex-shrink-0">
                                                            <div className="w-12 h-12 bg-red-500/20 rounded-full flex items-center justify-center">
                                                                <i className="fas fa-exclamation-triangle text-red-400 text-xl"></i>
                                                            </div>
                                                        </div>
                                                        <div className="flex-1">
                                                            <h3 className="text-lg font-medium text-white mb-2">
                                                                Deletar Conta
                                                            </h3>
                                                            <p className="text-gray-400 text-sm mb-4">
                                                                Uma vez que você deletar sua conta, não há como voltar atrás. Por favor, tenha certeza.
                                                            </p>
                                                            
                                                            <button
                                                                type="button"
                                                                onClick={() => setShowDeleteModal(true)}
                                                                className="bg-red-600 hover:bg-red-500 text-white font-medium py-2 px-4 rounded-xl transition-colors duration-200 flex items-center space-x-2"
                                                            >
                                                                <i className="fas fa-trash mr-2"></i>
                                                                <span>Deletar Minha Conta</span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Modals */}
            <ModalChangeEmail
                show={showEmailModal}
                onClose={() => setShowEmailModal(false)}
                form={emailForm}
                onSubmit={handleEmailSubmit}
                isLoading={isLoading}
                user={auth.user}
            />

            <ModalDeleteAccount
                show={showDeleteModal}
                onClose={() => {
                    setShowDeleteModal(false);
                    resetDeleteForm();
                }}
                form={deleteForm}
                onSubmit={handleDeleteSubmit}
                isLoading={isLoading}
                user={auth.user}
            />

            <style jsx>{`
                .tab-content {
                    animation: fadeIn 0.3s ease-in-out;
                }

                @keyframes fadeIn {
                    from { opacity: 0; transform: translateY(10px); }
                    to { opacity: 1; transform: translateY(0); }
                }

                .teal-glow-hover:hover {
                    box-shadow: 0 0 20px rgba(0, 230, 216, 0.3);
                }

                input:focus, select:focus {
                    box-shadow: 0 0 0 3px rgba(0, 230, 216, 0.1);
                }
            `}</style>
        </DashboardLayout>
    );
};