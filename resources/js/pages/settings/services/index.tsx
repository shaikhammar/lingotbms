import { Head, Link } from '@inertiajs/react';
import type { JSX } from 'react/jsx-runtime';
import { Button } from '@/components/ui/button';
import serviceRoutes from '@/routes/settings/services';
import type { Service } from '@/types';

export default function Services( { services }: { services: Array<Service> } ): JSX.Element {
    return (
        <>
            <Head title="Services" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
               <h1>Services Page</h1>

                    <Button
                    asChild 
                    className='w-1/2'>
                <Link href={serviceRoutes.create()} >
                       Create Service
                   </Link>
                   </Button>               

               <ul className="list-disc pl-5 space-y-2">
                   {services.map((service) => (
                       <li key={service.id}><Link href={serviceRoutes.edit(service)}>{service.name}</Link> - {service.default_unit} -- {service.is_active ? <Link href={serviceRoutes.archive(service)}>Archive</Link> : <Link href={serviceRoutes.restore(service)}>Restore</Link>}</li>
                   ))}
               </ul>
            </div>
        </>
    );
}

Services.layout = {
    breadcrumbs: [
        {
            title: 'Services',
            href: serviceRoutes.index(),
        },
    ],
};
