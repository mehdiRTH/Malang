import { IconDefinition } from "@fortawesome/fontawesome-svg-core";

export interface MenuItemInterface{
    label:string;
    icon:IconDefinition;
    count:number | null;
    route:string;
    isShowed:boolean
}
