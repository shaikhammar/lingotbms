import { Form, Head, router } from '@inertiajs/react';
import ServiceController from '@/actions/App/Modules/References/Http/Controllers/ServiceController';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

type PageProps = {
    units: string[];
};

export default function Create({ units }: PageProps) {
    
    return (
        <>
            <Head title="Create Service" />

            <h1 className="sr-only">Create Service</h1>

            <div className="max-w-3xl space-y-8">
                <Heading
                    variant="small"
                    title="Services"
                    description="Create new service."
                />

                <Form
                    {...ServiceController.store.form()}
                    method='post'
                    encType="multipart/form-data"
                    options={{
                        preserveScroll: true,
                    }}
                    className="space-y-8"
                >
                    {({ processing, errors }) => (
                        <>
                            {/* General Information Section */}
                            <div className="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div className="grid gap-2 sm:col-span-2">
                                    <Label htmlFor="name">Name</Label>
                                    <Input
                                        id="name"
                                        className="mt-1 block w-full"
                                        name="name"
                                        required
                                        autoComplete="name"
                                        placeholder="Service name"
                                    />
                                    <InputError className="mt-1" message={errors.name} />
                                </div>
</div>
                                

                            {/* Localization & Invoicing Section */}
                            <div className="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div className="grid gap-2">
                                    <Label htmlFor="default_unit">Default Unit</Label>
                                    <Select name="default_unit">
                                        <SelectTrigger className="w-full">
                                            <SelectValue placeholder="Select currency" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectGroup>
                                                {units.map((unit) => (
                                                    <SelectItem key={unit} value={unit}>
                                                        {unit}
                                                    </SelectItem>
                                                ))}
                                            </SelectGroup>
                                        </SelectContent>
                                    </Select>
                                    <InputError className="mt-1" message={errors.default_unit} />
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

Create.layout = {
    breadcrumb: [
        {
            title: 'Business',
            href: '/settings/business',
        },
    ],
};