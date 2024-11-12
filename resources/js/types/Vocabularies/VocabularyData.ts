import { LanguageEnum } from "../LanguageEnum";
import { GrammarInterface } from "./GrammarInterface";
import { TranslationsInterface } from "./TranslationssInterface";

export interface VocabularyData{
    id: number;
    name:string;
    translations:Array<TranslationsInterface>;
    vocabulary_grammar:GrammarInterface;
}

