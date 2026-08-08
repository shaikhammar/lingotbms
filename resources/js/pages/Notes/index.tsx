import { Head } from '@inertiajs/react';
import { JSX } from 'react/jsx-runtime';
import  notes  from '@/routes/notes';

export default function Notes( { notes }: { notes: Array<{ id: number; body: string }> } ): JSX.Element {
    return (
        <>
            <Head title="Notes" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
               <h1>Notes Page</h1>

               <ul className="list-disc pl-5 space-y-2">
                   {notes.map((note) => (
                       <li key={note.id}>{note.body}</li>
                   ))}
               </ul>
            </div>
        </>
    );
}

Notes.layout = {
    breadcrumbs: [
        {
            title: 'Notes',
            href: notes.index(),
        },
    ],
};
