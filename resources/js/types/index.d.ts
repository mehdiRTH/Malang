export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
}

export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    auth: {
        examen_results: any;
        type_quiz: any;
        wrong_answers: any;
        quiz_percentage: any;
        user: User;
    };
};
