export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
}
// types.ts
interface FileWithPreview extends File {
  preview?: string;
}
export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
    };
};
