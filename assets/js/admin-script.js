import {exportTags} from "./modules/admin/exportTags";
import {exportEvents} from "./modules/admin/exportEvents";
import {userRoles} from "./modules/userRoles";
import {eventsBulkExport} from "./modules/admin/eventsBulkExport";
import {resourcesDelete} from "./modules/admin/resourcesDelete";

document.addEventListener('DOMContentLoaded', () => {
	exportTags();
	exportEvents();
	userRoles();
	eventsBulkExport();
	resourcesDelete();
});
