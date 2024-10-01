import { LanguageEnum } from "../LanguageEnum";

export interface VocabularyData{
    id: number;
    name:string;
    languages_available:Array<LanguageEnum>;
    date:Date;
}

