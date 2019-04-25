// https://laravel.com/docs/5.4/mix#working-with-scripts
// https://andy-carter.com/blog/scoping-javascript-functionality-to-specific-pages-with-laravel-and-cakephp

import * as calendar from "./pages/calendar";
import * as flowchart from "./pages/flowchart";
import * as flowchartList from "./pages/flowchartList";
import * as flowchartEdit from "./pages/flowchartEdit";
import * as groupSession from "./pages/groupsession";
import * as profile from "./pages/profile";
import * as scheduler from "./pages/scheduler"

import * as advisorEdit from "./pages/dashboard/advisoredit";
import * as blackoutEdit from "./pages/dashboard/blackoutedit";
import * as completedCourseEdit from "./pages/dashboard/completedcourseedit";
import * as degreeProgramDetail from "./pages/dashboard/degreeprogramdetail";
import * as degreeProgramEdit from "./pages/dashboard/degreeprogramedit";
import * as departmentEdit from "./pages/dashboard/departmentedit";
import * as electiveListDetail from "./pages/dashboard/electivelistdetail";
import * as electiveListEdit from "./pages/dashboard/electivelistedit";
import * as groupSessionEdit from "./pages/dashboard/groupsessionedit";
import * as meetingEdit from "./pages/dashboard/meetingedit";
import * as planDetail from "./pages/dashboard/plandetail";
import * as planEdit from "./pages/dashboard/planedit";
import * as planSemesterEdit from "./pages/dashboard/plansemesteredit";
import * as settings from "./pages/dashboard/settings";
import * as studentEdit from "./pages/dashboard/studentedit";

import * as dashboard from "./util/dashboard";
import * as editable from "./util/editable";
import * as site from "./util/site";

// Load site-wide libraries in bootstrap file
import "./bootstrap";

const App = {

    // Controller-action methods
    actions: {
        // Index for directly created views with no explicit controller
        RootRouteController: {
            getIndex() {
                editable.init();
                site.checkMessage();
            },
            getAbout() {
                editable.init();
                site.checkMessage();
            },
        },

        // Advising Controller for routes at /advising
        AdvisingController: {
            // /advising/index
            getIndex() {
                calendar.init();
            },
        },

        // Groupsession Controller for routes at /groupsession
        GroupsessionController: {
            // /groupsession/index
            getIndex() {
                editable.init();
                site.checkMessage();
            },
            //groupsesion/list
            getList() {
                groupSession.init();
            },
        },

        // Profiles Controller for routes at /profile
        ProfilesController: {
            // /profile/index
            getIndex() {
                profile.init();
            },
        },

        // Dashboard Controller for routes at /admin-lte
        DashboardController: {
            // /admin/index
            getIndex() {
                dashboard.init();
            },
        },

        StudentsController: {
            // /admin/students
            getStudents() {
                studentEdit.init();
            },
            // /admin/newstudent
            getNewstudent() {
                studentEdit.init();
            },
        },

        AdvisorsController: {
            // /admin/advisors
            getAdvisors() {
                advisorEdit.init();
            },
            // /admin/newadvisor
            getNewadvisor() {
                advisorEdit.init();
            },
        },

        DepartmentsController: {
            // /admin/departments
            getDepartments() {
                departmentEdit.init();
            },
            // /admin/newdepartment
            getNewdepartment() {
                departmentEdit.init();
            },
        },

        MeetingsController: {
            // /admin/meetings
            getMeetings() {
                meetingEdit.init();
            },
        },

        BlackoutsController: {
            // /admin/blackouts
            getBlackouts() {
                blackoutEdit.init();
            },
        },

        GroupsessionsController: {
            // /admin/groupsessions
            getGroupsessions() {
                groupSessionEdit.init();
            },
        },

        SettingsController: {
            // /admin/settings
            getSettings() {
                settings.init();
            },
        },

        DegreeprogramsController: {
            // /admin/degreeprograms
            getDegreeprograms() {
                degreeProgramEdit.init();
            },
            // /admin/degreeprogram/{id}
            getDegreeprogramDetail() {
                degreeProgramDetail.init();
            },
            // /admin/newdegreeprogram
            getNewdegreeprogram() {
                degreeProgramEdit.init();
            },
        },

        ElectivelistsController: {
            // /admin/degreeprograms
            getElectivelists() {
                electiveListEdit.init();
            },
            // /admin/degreeprogram/{id}
            getElectivelistDetail() {
                electiveListDetail.init();
            },
            // /admin/newdegreeprogram
            getNewelectivelist() {
                electiveListEdit.init();
            },
        },

        PlansController: {
            // /admin/plans
            getPlans() {
                planEdit.init();
            },
            // /admin/plan/{id}
            getPlanDetail() {
                planDetail.init();
            },
            // /admin/newplan
            getNewplan() {
                planEdit.init();
            },
        },

        PlansemestersController: {
            // /admin/plansemester
            getPlanSemester() {
                planSemesterEdit.init();
            },
            // /admin/newplansemester
            getNewPlanSemester() {
                planSemesterEdit.init();
            },
        },

        CompletedcoursesController: {
            // /admin/completedcourses
            getCompletedcourses() {
                completedCourseEdit.init();
            },
            // /admin/newcompletedcourse
            getNewcompletedcourse() {
                completedCourseEdit.init();
            },
        },

        FlowchartsController: {
            // /flowcharts/view/
            getFlowchart() {
                flowchart.init()
            },
            getIndex() {
                flowchartList.init();
            },
            newFlowchart() {
                flowchartEdit.init();
            },
            editFlowchart() {
                flowchartEdit.init();
            },
        },

        SchedulerController: {
            show() {
                scheduler.init();
            },
        },
    },

    // Function that is called by the page at load. Defined in resources/views/includes/scripts.blade.php
    // and App/Http/ViewComposers/Javascript Composer
    // See links at top of file for description of what's going on here
    // Assumes 2 inputs - the controller and action that created this page
    init(controller, action) {
        if (this.actions[controller] != null && this.actions[controller][action] != null) {
            // call the matching function in the array above
            return App.actions[controller][action]();
        }
    },
};

// Bind to the window
window.App = App;

App.init(appInit.controller, appInit.action);
