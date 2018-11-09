import { fileSize } from 'src/core/service/utils/format.utils';
import { Filter } from 'src/core/shopware';

Filter.register('fileSize', (value, locale) => {
    return fileSize(value, locale);
});
