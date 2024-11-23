import { MetaInterface } from "./MetaInterface"

export interface DashboardDataInterface{
    data:Array<{
        vocabularies_number:number,
        date:Date,
        score:number
    }>,
    meta:MetaInterface
}
