import { Form, Head, usePage } from '@inertiajs/react';
import TenantSettingController from '@/actions/App/Http/Controllers/Settings/TenantSettingController';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { TenantSetting } from '@/types/tenantsetting';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { Separator } from '@/components/ui/separator';
import { useEffect, useRef, useState } from 'react';

type PageProps = {
    tenantSetting: TenantSetting;
    currencies: string[];
    timezones: string[];
};

export default function Business({ tenantSetting, currencies, timezones }: PageProps) {
    const [previewUrl, setPreviewUrl] = useState<string | null>(null);
    const [isRemoving, setIsRemoving] = useState(false);
    const fileInputRef = useRef<HTMLInputElement>(null);
    
    // Clean up the object URL to prevent memory leaks when the component unmounts or URL changes
    useEffect(() => {
        return () => {
            if (previewUrl) {
                URL.revokeObjectURL(previewUrl);
            }
        };
    }, [previewUrl]);
    
    const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        
        if (file) {
            // Revoke the old preview if the user selects a different file
            if (previewUrl) {
                URL.revokeObjectURL(previewUrl);
            }
            setPreviewUrl(URL.createObjectURL(file));
            setIsRemoving(false); // Uncheck removal if they select a new file
        } else {
            setPreviewUrl(null);
        }
    };
    
    const handleRemoveLogo = () => {
        setIsRemoving(true);
        setPreviewUrl(null);
        if (fileInputRef.current) {
            fileInputRef.current.value = ''; // Reset the file input
        }
    };
    
    // Determine what to show in the UI: the local preview, the saved logo, or nothing.
    const displayUrl = previewUrl || (isRemoving ? null : tenantSetting.logo_url);
    
    return (
        <>
            <Head title="Profile settings" />

            <h1 className="sr-only">Profile settings</h1>

            <div className="max-w-3xl space-y-8">
                <Heading
                    variant="small"
                    title="Business"
                    description="Update your business information and invoicing preferences."
                />

                <Form
                    {...TenantSettingController.update.form()}
                    method='post'
                    encType="multipart/form-data"
                    options={{
                        preserveScroll: true,
                    }}
                    className="space-y-8"
                >
                    {({ processing, errors, progress }) => (
                        <>
                            {/* General Information Section */}
                            <div className="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div className="grid gap-2 sm:col-span-2">
                                    <Label htmlFor="business_name">Name</Label>
                                    <Input
                                        id="business_name"
                                        className="mt-1 block w-full"
                                        defaultValue={tenantSetting.business_name}
                                        name="business_name"
                                        required
                                        autoComplete="name"
                                        placeholder="Full business name"
                                    />
                                    <InputError className="mt-1" message={errors.business_name} />
                                </div>

                                <div className="grid gap-2 sm:col-span-2">
                                    <Label htmlFor="address">Address</Label>
                                    <Textarea
                                        id="address"
                                        className="mt-1 block w-full resize-y"
                                        defaultValue={tenantSetting.address}
                                        name="address"
                                        required
                                        autoComplete="street-address"
                                        placeholder="Business address"
                                        rows={3}
                                    />
                                    <InputError className="mt-1" message={errors.address} />
                                </div>

                                {/* Updated Logo Section */}
                                <div className="grid gap-3 sm:col-span-2">
                                    <Label htmlFor="logo_path">Logo</Label>

                                    {/* Logo Preview Area */}
                                    {displayUrl && (
                                        <div className="relative h-24 w-24 overflow-hidden rounded-md border border-border bg-muted flex items-center justify-center">
                                            <img 
                                                src={displayUrl} 
                                                alt="Business Logo Preview" 
                                                className="h-full w-full object-contain" 
                                            />
                                        </div>
                                    )}

                                    <div className="flex items-center gap-4">
                                        <Input
                                            id="logo_path"
                                            ref={fileInputRef}
                                            className="block w-full max-w-sm cursor-pointer"
                                            name="logo"
                                            accept="image/png,image/jpg,image/jpeg,image/svg+xml"
                                            type="file"
                                            onChange={handleFileChange}
                                        />
                                        
                                        {(displayUrl || tenantSetting.logo_path) && !isRemoving && (
                                            <Button 
                                                type="button" 
                                                variant="destructive" 
                                                size="sm" 
                                                onClick={handleRemoveLogo}
                                            >
                                                Remove
                                            </Button>
                                        )}
                                    </div>

                                    {/* Hidden input to tell the backend to delete the logo */}
                                    <input type="hidden" name="remove_logo" value={isRemoving ? '1' : '0'} />

                                    {/* Progress Bar Display */}
                                    {progress && progress.percentage !== undefined && progress.percentage > 0 && progress.percentage < 100 && (
                                        <div className="h-2 w-full max-w-sm overflow-hidden rounded-full bg-secondary">
                                            <div 
                                                className="h-full bg-primary transition-all duration-300" 
                                                style={{ width: `${progress.percentage}%` }} 
                                            />
                                        </div>
                                    )}

                                    <InputError className="mt-1" message={errors.logo_path || errors.logo} />
                                </div>
                            </div>

                            <Separator />

                            {/* Localization & Invoicing Section */}
                            <div className="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div className="grid gap-2">
                                    <Label htmlFor="base_currency">Base Currency</Label>
                                    <Select defaultValue={tenantSetting.base_currency} name="base_currency">
                                        <SelectTrigger className="w-full">
                                            <SelectValue placeholder="Select currency" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectGroup>
                                                {currencies.map((currency) => (
                                                    <SelectItem key={currency} value={currency}>
                                                        {currency}
                                                    </SelectItem>
                                                ))}
                                            </SelectGroup>
                                        </SelectContent>
                                    </Select>
                                    <InputError className="mt-1" message={errors.base_currency} />
                                </div>

                                <div className="grid gap-2">
                                    <Label htmlFor="timezone">Timezone</Label>
                                    <Select defaultValue={tenantSetting.timezone} name="timezone">
                                        <SelectTrigger className="w-full">
                                            <SelectValue placeholder="Select timezone" />
                                        </SelectTrigger>
                                        <SelectContent position="item-aligned">
                                            <SelectGroup>
                                                {timezones.map((timezone) => (
                                                    <SelectItem key={timezone} value={timezone}>
                                                        {timezone}
                                                    </SelectItem>
                                                ))}
                                            </SelectGroup>
                                        </SelectContent>
                                    </Select>
                                    <InputError className="mt-1" message={errors.timezone} />
                                </div>

                                <div className="grid gap-2">
                                    <Label htmlFor="invoice_prefix">Invoice Prefix</Label>
                                    <Input
                                        id="invoice_prefix"
                                        className="w-full"
                                        defaultValue={tenantSetting.invoice_prefix}
                                        name="invoice_prefix"
                                        autoComplete="off"
                                        placeholder="e.g. INV-"
                                    />
                                    <InputError className="mt-1" message={errors.invoice_prefix} />
                                </div>

                                <div className="grid gap-2">
                                    <Label htmlFor="invoice_next_number">Invoice Next Number</Label>
                                    <Input
                                        id="invoice_next_number"
                                        className="w-full"
                                        defaultValue={tenantSetting.invoice_next_number}
                                        name="invoice_next_number"
                                        autoComplete="off"
                                        type="number"
                                        placeholder="e.g. 1001"
                                    />
                                    <InputError className="mt-1" message={errors.invoice_next_number} />
                                </div>

                                <div className="grid gap-2 sm:col-span-2">
                                    <Label htmlFor="default_payment_terms_days">Default Payment Terms (Days)</Label>
                                    <Input
                                        id="default_payment_terms_days"
                                        className="w-full sm:w-1/2"
                                        defaultValue={tenantSetting.default_payment_terms_days}
                                        name="default_payment_terms_days"
                                        autoComplete="off"
                                        type="number"
                                        placeholder="e.g. 30"
                                    />
                                    <InputError className="mt-1" message={errors.default_payment_terms_days} />
                                </div>
                            </div>

                            <div className="flex items-center gap-4 pt-2">
                                <Button
                                    disabled={processing}
                                    data-test="update-profile-button"
                                    className="min-w-[120px]"
                                >
                                    Save Changes
                                </Button>
                            </div>
                        </>
                    )}
                </Form>
            </div>
        </>
    );
}

Business.layout = {
    breadcrumb: [
        {
            title: 'Business',
            href: '/settings/business',
        },
    ],
};