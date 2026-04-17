declare module 'persian-date' {
    interface PersianDateOptions {
        timezone?: string;
        locale?: string;
        formatNumber?: boolean;
    }

    type CalendarType = 'gregorian' | 'iso' | 'persian' | 'arabic' | 'hijri' | 'jalali';
    type LeapYearMode = 'astronomical' | 'algorithmic';

    class PersianDate {
        constructor(input?: string | number | Date | number[] | PersianDateOptions);
        toCalendar(type: CalendarType): this;
        year(): number;
        month(): number;
        date(): number;
        day(): number;
        hour(): number;
        minute(): number;
        second(): number;
        millisecond(): number;
        endOf(unit: 'month' | 'year' | 'day'): this;
        startOf(unit: 'month' | 'year' | 'day'): this;
        format(pattern: string): string;
        valueOf(): number;
    }

    export default PersianDate;
}
