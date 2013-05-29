# encoding: utf-8
import json
import urllib
import urllib2
import cookielib
import sys
import socket
import gc
import copy
import webbrowser
import datetime
import time
import traceback
import base64
import re
import gobject

# to let Linux distributions use their own BeautifulSoup if existent try importing local BeautifulSoup first
# see https://sourceforge.net/tracker/?func=detail&atid=1101370&aid=3302612&group_id=236865
try:
    from BeautifulSoup import BeautifulSoup, BeautifulStoneSoup
except:
    from Nagstamon.BeautifulSoup import BeautifulSoup, BeautifulStoneSoup
from Nagstamon.Actions import HostIsFilteredOutByRE, ServiceIsFilteredOutByRE, StatusInformationIsFilteredOutByRE, not_empty
from Nagstamon.Objects import *
from Nagstamon.Server.Generic import GenericServer, not_empty

# fix/patch for https://bugs.launchpad.net/ubuntu/+source/nagstamon/+bug/732544
socket.setdefaulttimeout(30)


class IsouServer(GenericServer):
    """
        Isou plugin for Nagstamon
    """
    
    TYPE = 'Isou'
    
    # GUI sortable columns stuff
    DEFAULT_SORT_COLUMN_ID = 2
    COLOR_COLUMN_ID = 2
    HOST_COLUMN_ID = 0
    SERVICE_COLUMN_ID = 1
    
    COLUMNS = [
        HostColumn,
        ServiceColumn,
        StatusColumn,
        LastCheckColumn,
        DurationColumn,
        AttemptColumn,
        StatusInformationColumn
    ]
    
    DISABLED_CONTROLS = []
    
    # dictionary to translate status bitmaps on webinterface into status flags
    # this are defaults from Nagios
    # "disabled.gif" is in Nagios for hosts the same as "passiveonly.gif" for services
    STATUS_MAPPING = { "ack.gif" : "acknowledged",\
                       "passiveonly.gif" : "passiveonly",\
                       "disabled.gif" : "passiveonly",\
                       "ndisabled.gif" : "notifications_disabled",\
                       "downtime.gif" : "scheduled_downtime",\
                       "flapping.gif" : "flapping"}

    # Entries for monitor default actions in context menu
    MENU_ACTIONS = []
    
    # Arguments available for submitting check results 
    SUBMIT_CHECK_RESULT_ARGS = ["check_output", "performance_data"]
    
    # URLs for browser shortlinks/buttons on popup window
    BROWSER_URLS= { "monitor": "$MONITOR$"}

    def __init__(self, **kwds):
        # add all keywords to object, every mode searchs inside for its favorite arguments/keywords
        for k in kwds: self.__dict__[k] = kwds[k]
        
        self.type = ""
        self.monitor_url = ""
        self.monitor_cgi_url = ""
        self.username = ""
        self.password = ""
        self.use_proxy = False
        self.use_proxy_from_os = False
        self.proxy_address = ""
        self.proxy_username = ""
        self.proxy_password = ""        
        self.hosts = dict()
        self.new_hosts = dict()
        self.thread = None
        self.isChecking = False
        self.CheckingForNewVersion = False
        self.WorstStatus = "UP"
        self.States = ["UP", "UNKNOWN", "WARNING", "CRITICAL", "UNREACHABLE", "DOWN"]
        self.nagitems_filtered_list = list()
        self.nagitems_filtered = {"services":{"CRITICAL":[], "WARNING":[], "UNKNOWN":[]}, "hosts":{"DOWN":[], "UNREACHABLE":[]}}
        self.downs = 0
        self.unreachables = 0
        self.unknowns = 0
        self.criticals = 0
        self.warnings = 0
        self.status = ""
        self.status_description = ""
        # needed for looping server thread
        self.count = 0
        # needed for RecheckAll - save start_time once for not having to get it for every recheck
        self.start_time = None
        self.Cookie = cookielib.CookieJar()        
        # use server-owned attributes instead of redefining them with every request
        self.passman = None   
        self.basic_handler = None
        self.digest_handler = None
        self.proxy_handler = None
        self.proxy_auth_handler = None        
        self.urlopener = None
        # headers for HTTP requests, might be needed for authorization on Nagios/Icinga Hosts
        self.HTTPheaders = {}
        # attempt to use only one bound list of TreeViewColumns instead of ever increasing one
        self.TreeView = None
        self.TreeViewColumns = list()
        self.ListStore = None
        self.ListStoreColumns = list()
        # flag which decides if authentication has to be renewed
        self.refresh_authentication = False

    
    def init_HTTP(self):
        """
        partly not constantly working Basic Authorization requires extra Autorization headers,
        different between various server types
        """
        """
        if self.HTTPheaders == {}:
            for giveback in ["raw", "obj"]:
                self.HTTPheaders[giveback] = {"Authorization": "Basic " + base64.b64encode(self.get_username() + ":" + self.get_password())}
        """

                
    def reset_HTTP(self):
        """
        if authentication fails try to reset any HTTP session stuff - might be different for different monitors
        """
        self.HTTPheaders = {}     
        
                
    def get_name(self):
        """
        return stringified name
        """
        return str(self.name)    
    
    
    def get_username(self):
        """
        return stringified username
        """
        return str(self.username)  
        
    
    def get_password(self):
        """
        return stringified username
        """
        return str(self.password)  
        

    @classmethod
    def get_columns(cls, row):
        """ Gets columns filled with row data """  
        for column_class in cls.COLUMNS:
            # str() necessary because MacOSX Python cries otherwise
            yield str(column_class(row))        

        
    def set_recheck(self, thread_obj):
        # nothing
        return None
        
    def _set_recheck(self, host, service):
        # nothing
        return None
        
    def set_acknowledge(self, thread_obj):
        # nothing
        return None

    def _set_acknowledge(self, host, service, author, comment, sticky, notify, persistent, all_services=[]):
        # nothing
        return None

    def set_downtime(self, thread_obj):
        # nothing
        return None

    def _set_downtime(self, host, service, author, comment, fixed, start_time, end_time, hours, minutes):
        # nothing
        return None

    def set_submit_check_result(self, thread_obj):
        self._set_submit_check_result(thread_obj.host, thread_obj.service, thread_obj.state, thread_obj.comment,\
                                  thread_obj.check_output, thread_obj.performance_data)
        
        
    def _set_submit_check_result(self, host, service, state, comment, check_output, performance_data):
        """
        worker for submitting check result
        """
        print 'check_result: %s'%(url)
        self.FetchURL(url, giveback="raw", cgi_data=self.monitor_cgi_url)

        
    def get_start_end(self, host):
        """
        for GUI to get actual downtime start and end from server - they may vary so it's better to get
        directly from web interface
        """
        try:
            result = self.FetchURL(self.monitor_cgi_url + "/cmd.cgi?" + urllib.urlencode({"cmd_typ":"55", "host":host}))
            html = result.result
            start_time = html.find(attrs={"name":"start_time"}).attrMap["value"]
            end_time = html.find(attrs={"name":"end_time"}).attrMap["value"]            
            # give values back as tuple
            return start_time, end_time
        except:
            self.Error(sys.exc_info())
            return "n/a", "n/a"    

        
    def open_tree_view(self, host, service=""):
        """
        open monitor from treeview context menu
        """
        # only type is important so do not care of service "" in case of host monitor       
        if service == "":
            typ = 1
        else:
            typ = 2      
        if str(self.conf.debug_mode) == "True":
            self.Debug(server=self.get_name(), host=host, service=service, debug="Open host/service monitor web page " + self.monitor_cgi_url + '/extinfo.cgi?' + urllib.urlencode({"type":typ, "host":host, "service":service}))        
        webbrowser.open(self.monitor_cgi_url + '/extinfo.cgi?' + urllib.urlencode({"type":typ, "host":host, "service":service}))

        
    def OpenBrowser(self, widget=None, url_type="", output=None):
        """
        multiple purpose open browser method for all open-a-browser-needs
        """                

        # first close popwin
        if output <> None:
            output.popwin.Close()
        
        # run thread with action
        action = Actions.Action(string=self.BROWSER_URLS[url_type],\
                        type="browser",\
                        conf=self.conf,\
                        server=self)
        action.run()
    
            
    def _get_status(self):
        """
        Get status from Nagios Server
        """
        # create Nagios items dictionary with to lists for services and hosts
        # every list will contain a dictionary for every failed service/host
        # this dictionary is only temporarily
        nagitems = {"hosts":[], "services":[]}

        # new_hosts dictionary
        self.new_hosts = dict()

        # create filters like described in
        # http://www.nagios-wiki.de/nagios/tips/host-_und_serviceproperties_fuer_status.cgi?s=servicestatustypes
        # hoststatus
        hoststatustypes = 12
        # servicestatus
        servicestatustypes = 253
        # serviceprops & hostprops both have the same values for the same states so I
        # group them together
        hostserviceprops = 0
        # services (unknown, warning or critical?)
        nagcgiurl_isou = '%s/index.php/liste'%(self.monitor_url)
        # hosts - mostly the down ones
        # unfortunately the hosts status page has a different structure so
        # hosts must be analyzed separately
        try:
            # result = self.FetchURL(nagcgiurl_isou)
            result = self.FetchURL('%s/isou.json'%(self.monitor_url))
            htobj, error = result.result, result.error

            if error != "": return Result(result=htobj, error=error)

            services = json.loads(htobj.string.strip())
            # do some cleanup
            del htobj

            for service in services['fisou']['services']:
                try:
                    n = {}
                    # host
                    # the resulting table of Nagios status.cgi table omits the
                    # hostname of a failing service if there are more than one
                    # so if the hostname is empty the nagios status item should get
                    # its hostname from the previuos item - one reason to keep "nagitems"
                    n["host"] = 'isou'

                    # service
                    # get tds in one tr
                    n["service"] = str(service['name'])

                    # status
                    n["scheduled_downtime"] = False
                    if service['state'] == '0':
                        n["status"] = "OK"
                    elif service['state'] == '1':
                        n["status"] = "WARNING"
                    elif service['state'] == '2':
                        n["status"] = "CRITICAL"
                    elif service['state'] == '3':
                        n["status"] = "UNKNOWN"
                    elif service['state'] == '4':
                        n["status"] = "4"
                        n["scheduled_downtime"] = True
                    # last_check
                    n["last_check"] = "0"
                    # duration
                    n["duration"] = "0" # service['date']
                    # attempt
                    # to fix http://sourceforge.net/tracker/?func=detail&atid=1101370&aid=3280961&group_id=236865 .attempt needs
                    # to be stripped
                    n["attempt"] = "0/0"

                    # status_information
                    n["status_information"] = (',').join(service['description'])

                    # status flags
                    n["passiveonly"] = False
                    n["notifications_disabled"] = False
                    n["flapping"] = False
                    n["acknowledged"] = False
                    n["scheduled_downtime"] = False

                    # add dictionary full of information about this host item to nagitems
                    nagitems["services"].append(n)
                    # after collection data in nagitems create objects of its informations
                    # host objects contain service objects
                    if not self.new_hosts.has_key(n["host"]):
                        self.new_hosts[n["host"]] = GenericHost()
                        self.new_hosts[n["host"]].name = n["host"]
                        self.new_hosts[n["host"]].status = "UP"

                    # if a service does not exist create its object
                    if not self.new_hosts[n["host"]].services.has_key(n["service"]):
                        new_service = n["service"]
                        self.new_hosts[n["host"]].services[new_service] = GenericService()
                        self.new_hosts[n["host"]].services[new_service].host = n["host"]
                        self.new_hosts[n["host"]].services[new_service].name = n["service"]
                        self.new_hosts[n["host"]].services[new_service].status = n["status"]
                        self.new_hosts[n["host"]].services[new_service].last_check = n["last_check"]
                        self.new_hosts[n["host"]].services[new_service].duration = n["duration"]
                        self.new_hosts[n["host"]].services[new_service].attempt = n["attempt"]
                        self.new_hosts[n["host"]].services[new_service].status_information = n["status_information"].encode("utf-8")
                        self.new_hosts[n["host"]].services[new_service].passiveonly = n["passiveonly"]
                        self.new_hosts[n["host"]].services[new_service].notifications_disabled = n["notifications_disabled"]
                        self.new_hosts[n["host"]].services[new_service].flapping = n["flapping"]
                        self.new_hosts[n["host"]].services[new_service].acknowledged = n["acknowledged"]
                        self.new_hosts[n["host"]].services[new_service].scheduled_downtime = n["scheduled_downtime"]
                except:
                    self.Error(sys.exc_info())

            # do some cleanup
            del services

        except:
            # set checking flag back to False
            self.isChecking = False
            result, error = self.Error(sys.exc_info())
            return Result(result=result, error=error)

        # some cleanup
        del nagitems

        #dummy return in case all is OK
        return Result()


    def GetStatus(self, output=None):
        """
        get nagios status information from nagcgiurl and give it back
        as dictionary
        output parameter is needed in case authentication failed so that popwin might ask for credentials
        """

        # set checking flag to be sure only one thread cares about this server
        self.isChecking = True

        # check if server is enabled, if not, do not get any status
        if str(self.conf.servers[self.get_name()].enabled) == "False":
            self.WorstStatus = "UP"
            self.isChecking = False
            return Result()

        # get all trouble hosts/services from server specific _get_status()
        status = self._get_status()
        self.status, self.status_description = status.result, status.error

        if status.error != "":
            # ask for password if authorization failed
            if "HTTP Error 401" in status.error or \
               "HTTP Error 403" in status.error or \
               "Bad Session ID" in status.error:
                if str(self.conf.servers[self.name].enabled) == "True":
                    while status.error != "":
                        # clean existent authentication
                        self.reset_HTTP() 
                        self.refresh_authentication = True
                        # needed to get valid credentials
                        gobject.idle_add(output.RefreshDisplayStatus)
                        # take a break not to DOS the monitor...
                        time.sleep(20)
                        status = self._get_status()
                        self.status, self.status_description = status.result, status.error  
                        # if monitor has been disabled do not try to connect to it
                        if str(self.conf.servers[self.name].enabled) == "False":
                            break
            else:
                self.isChecking = False
                return Result(result=self.status, error=self.status_description)

        # this part has been before in GUI.RefreshDisplay() - wrong place, here it needs to be reset
        self.nagitems_filtered = {"services":{"CRITICAL":[], "WARNING":[], "UNKNOWN":[]}, "hosts":{"DOWN":[], "UNREACHABLE":[]}}

        # initialize counts for various service/hosts states
        # count them with every miserable host/service respective to their meaning
        self.downs = 0
        self.unreachables = 0
        self.unknowns = 0
        self.criticals = 0
        self.warnings = 0

        for host in self.new_hosts.values():
            # Don't enter the loop if we don't have a problem. Jump down to your problem services
            if not host.status == "UP":
                # Some generic filters
                if host.acknowledged == True and str(self.conf.filter_acknowledged_hosts_services) == "True":
                    if str(self.conf.debug_mode) == "True":
                        self.Debug(server=self.get_name(), debug="Filter: ACKNOWLEDGED " + str(host.name))
                    host.visible = False
    
                if host.notifications_disabled == True and str(self.conf.filter_hosts_services_disabled_notifications) == "True":
                    if str(self.conf.debug_mode) == "True":
                        self.Debug(server=self.get_name(), debug="Filter: NOTIFICATIONS " + str(host.name))
                    host.visible = False
 
                if host.passiveonly == True and str(self.conf.filter_hosts_services_disabled_checks) == "True": 
                    if str(self.conf.debug_mode) == "True":
                        self.Debug(server=self.get_name(), debug="Filter: PASSIVEONLY " + str(host.name))
                    host.visible = False
    
                if host.scheduled_downtime == True and str(self.conf.filter_hosts_services_maintenance) == "True":
                    if str(self.conf.debug_mode) == "True":
                        self.Debug(server=self.get_name(), debug="Filter: DOWNTIME " + str(host.name))
                    host.visible = False
    
                if host.flapping == True and str(self.conf.filter_all_flapping_hosts) == "True":
                    if str(self.conf.debug_mode) == "True":
                        self.Debug(server=self.get_name(), debug="Filter: FLAPPING HOST " + str(host.name))
                    host.visible = False
    
                if HostIsFilteredOutByRE(host.name, self.conf) == True:
                    if str(self.conf.debug_mode) == "True":
                        self.Debug(server=self.get_name(), debug="Filter: REGEXP " + str(host.name))
                    host.visible = False
                    
                if StatusInformationIsFilteredOutByRE(host.status_information, self.conf) == True:
                    if str(self.conf.debug_mode) == "True":
                        self.Debug(server=self.get_name(), debug="Filter: REGEXP " + str(host.name))
                    host.visible = False 
    
                # Finegrain for the specific state
                if host.status == "DOWN":
                    if str(self.conf.filter_all_down_hosts) == "True":
                        if str(self.conf.debug_mode) == "True":
                            self.Debug(server=self.get_name(), debug="Filter: DOWN " + str(host.name))
                        host.visible = False
    
                    if host.visible:
                        self.nagitems_filtered["hosts"]["DOWN"].append(host)
                        self.downs += 1
    
                if host.status == "UNREACHABLE":
                    if str(self.conf.filter_all_unreachable_hosts) == "True":
                        if str(self.conf.debug_mode) == "True":
                            self.Debug(server=self.get_name(), debug="Filter: UNREACHABLE " + str(host.name))
                        host.visible = False
    
                    if host.visible:
                        self.nagitems_filtered["hosts"]["UNREACHABLE"].append(host)
                        self.unreachables += 1
    
            for service in host.services.values():                
                # Some generic filtering
                if service.acknowledged == True and str(self.conf.filter_acknowledged_hosts_services) == "True":
                    if str(self.conf.debug_mode) == "True":
                        self.Debug(server=self.get_name(), debug="Filter: ACKNOWLEDGED " + str(host.name) + ";" + str(service.name))
                    service.visible = False
    
                if service.notifications_disabled == True and str(self.conf.filter_hosts_services_disabled_notifications) == "True":
                    if str(self.conf.debug_mode) == "True":
                        self.Debug(server=self.get_name(), debug="Filter: NOTIFICATIONS " + str(host.name) + ";" + str(service.name))
                    service.visible = False
    
                if service.passiveonly == True and str(self.conf.filter_hosts_services_disabled_checks) == "True":              
                    if str(self.conf.debug_mode) == "True":
                        self.Debug(server=self.get_name(), debug="Filter: PASSIVEONLY " + str(host.name) + ";" + str(service.name))
                    service.visible = False
    
                if service.scheduled_downtime == True and str(self.conf.filter_hosts_services_maintenance) == "True":
                    if str(self.conf.debug_mode) == "True":
                        self.Debug(server=self.get_name(), debug="Filter: DOWNTIME " + str(host.name) + ";" + str(service.name))
                    service.visible = False
                
                if service.flapping == True and str(self.conf.filter_all_flapping_services) == "True":
                    if str(self.conf.debug_mode) == "True":
                        self.Debug(server=self.get_name(), debug="Filter: FLAPPING SERVICE " + str(host.name) + ";" + str(service.name))
                    service.visible = False
                    
                if host.scheduled_downtime == True and str(self.conf.filter_services_on_hosts_in_maintenance) == "True":
                    if str(self.conf.debug_mode) == "True":
                        self.Debug(server=self.get_name(), debug="Filter: Service on host in DOWNTIME " + str(host.name) + ";" + str(service.name))
                    service.visible = False

                if host.acknowledged == True and str(self.conf.filter_services_on_acknowledged_hosts) == "True":
                    if str(self.conf.debug_mode) == "True":
                        self.Debug(server=self.get_name(), debug="Filter: Service on acknowledged host" + str(host.name) + ";" + str(service.name))
                    service.visible = False                    
                    
                if host.status == "DOWN" and str(self.conf.filter_services_on_down_hosts) == "True":
                    if str(self.conf.debug_mode) == "True":
                        self.Debug(server=self.get_name(), debug="Filter: Service on host in DOWN " + str(host.name) + ";" + str(service.name))
                    service.visible = False
    
                if host.status == "UNREACHABLE" and str(self.conf.filter_services_on_unreachable_hosts) == "True":
                    if str(self.conf.debug_mode) == "True":
                        self.Debug(server=self.get_name(), debug="Filter: Service on host in UNREACHABLE " + str(host.name) + ";" + str(service.name))
                    service.visible = False
    
                real_attempt, max_attempt = service.attempt.split("/")
                if real_attempt <> max_attempt and str(self.conf.filter_services_in_soft_state) == "True":
                    if str(self.conf.debug_mode) == "True":
                        self.Debug(server=self.get_name(), debug="Filter: SOFT STATE " + str(host.name) + ";" + str(service.name))
                    service.visible = False
                
                if HostIsFilteredOutByRE(host.name, self.conf) == True:
                    if str(self.conf.debug_mode) == "True":
                        self.Debug(server=self.get_name(), debug="Filter: REGEXP " + str(host.name) + ";" + str(service.name))
                    service.visible = False
    
                if ServiceIsFilteredOutByRE(service.get_name(), self.conf) == True:
                    if str(self.conf.debug_mode) == "True":
                        self.Debug(server=self.get_name(), debug="Filter: REGEXP " + str(host.name) + ";" + str(service.name))
                    service.visible = False
                    
                if StatusInformationIsFilteredOutByRE(service.status_information, self.conf) == True:
                    if str(self.conf.debug_mode) == "True":
                        self.Debug(server=self.get_name(), debug="Filter: REGEXP " + str(host.name) + ";" + str(service.name))
                    service.visible = False                     
    
                # Finegrain for the specific state
                if service.visible:
                    if service.status == "CRITICAL":
                        if str(self.conf.filter_all_critical_services) == "True":
                            if str(self.conf.debug_mode) == "True":
                                self.Debug(server=self.get_name(), debug="Filter: CRITICAL " + str(host.name) + ";" + str(service.name))
                            service.visible = False
                        else:
                            self.nagitems_filtered["services"]["CRITICAL"].append(service)
                            self.criticals += 1
    
                    if service.status == "WARNING":
                        if str(self.conf.filter_all_warning_services) == "True":
                            if str(self.conf.debug_mode) == "True":
                                self.Debug(server=self.get_name(), debug="Filter: WARNING " + str(host.name) + ";" + str(service.name))
                            service.visible = False
                        else:
                            self.nagitems_filtered["services"]["WARNING"].append(service)
                            self.warnings += 1
    
                    if service.status == "UNKNOWN":
                        if str(self.conf.filter_all_unknown_services) == "True":
                            if str(self.conf.debug_mode) == "True":
                                self.Debug(server=self.get_name(), debug="Filter: UNKNOWN " + str(host.name) + ";" + str(service.name))
                            service.visible = False
                        else:
                            self.nagitems_filtered["services"]["UNKNOWN"].append(service)
                            self.unknowns += 1

        # find out if there has been some status change to notify user
        # compare sorted lists of filtered nagios items
        new_nagitems_filtered_list = []
        
        for i in self.nagitems_filtered["hosts"].values():
            for h in i:
                new_nagitems_filtered_list.append((h.name, h.status))   
            
        for i in self.nagitems_filtered["services"].values():
            for s in i:
                new_nagitems_filtered_list.append((s.host, s.name, s.status))  
                 
        # sort for better comparison
        new_nagitems_filtered_list.sort()

        # if both lists are identical there was no status change
        if (self.nagitems_filtered_list == new_nagitems_filtered_list):       
            self.WorstStatus = "UP"
        else:
            # if the new list is shorter than the first and there are no different hosts 
            # there one host/service must have been recovered, which is not worth a notification
            diff = []
            for i in new_nagitems_filtered_list:
                if not i in self.nagitems_filtered_list:
                    # collect differences
                    diff.append(i)
            if len(diff) == 0:
                self.WorstStatus = "UP"
            else:
                # if there are different hosts/services in list of new hosts there must be a notification
                # get list of states for comparison
                diff_states = []
                for d in diff:
                    diff_states.append(d[-1])
                # temporary worst state index   
                worst = 0
                for d in diff_states:
                    # only check worst state if it is valid
                    if d in self.States:
                        if self.States.index(d) > worst:
                            worst = self.States.index(d)
                            
                # final worst state is one of the predefined states
                self.WorstStatus = self.States[worst]
            
        # copy of listed nagitems for next comparison
        self.nagitems_filtered_list = copy.copy(new_nagitems_filtered_list)
        
        # put new informations into respective dictionaries      
        self.hosts = copy.copy(self.new_hosts)
        self.new_hosts.clear()
        
        # after all checks are done unset checking flag
        self.isChecking = False
        
        # return True if all worked well    
        return Result()
    
    
    def FetchURL(self, url, giveback="obj", cgi_data=None):   
        """
        get content of given url, cgi_data only used if present
        "obj" FetchURL gives back a dict full of miserable hosts/services,
        "xml" giving back as objectified xml
        "raw" it gives back pure HTML - useful for finding out IP or new version
        existence of cgi_data forces urllib to use POST instead of GET requests
        NEW: gives back a list containing result and, if necessary, a more clear error description
        """        
        
        # run this method which checks itself if there is some action to take for initializing connection
        self.init_HTTP()

        try:
            try:
                # debug
                if str(self.conf.debug_mode) == "True":
                    self.Debug(server=self.get_name(), debug="FetchURL: " + url + " CGI Data: " + str(cgi_data))
                request = urllib2.Request(url, cgi_data)
                # use opener - if cgi_data is not empty urllib uses a POST request
                urlcontent = self.urlopener.open(request)
                del url, cgi_data, request                               
            except:
                result, error = self.Error(sys.exc_info())
                return Result(result=result, error=error)
           
            # give back pure HTML or XML in case giveback is "raw"
            if giveback == "raw":                           
                result = Result(result=urlcontent.read())
                urlcontent.close()
                del urlcontent
                return result
            
            # objectified HTML
            if giveback == 'obj':
                yummysoup = BeautifulSoup(unicode(urlcontent.read(), "utf8", errors="ignore"), convertEntities=BeautifulSoup.ALL_ENTITIES)
                urlcontent.close()                
                del urlcontent
                #return Result(result=copy.deepcopy(yummysoup))
                return Result(result=yummysoup)

            # objectified generic XML, valid at least for Opsview and Centreon
            elif giveback == "xml":
                xmlobj = BeautifulStoneSoup(urlcontent.read(), convertEntities=BeautifulStoneSoup.XML_ENTITIES)
                urlcontent.close()
                del urlcontent  
                #return Result(result=copy.deepcopy(xmlobj)) 
                return Result(result=xmlobj)   

        except:
            # do some cleanup        
            result, error = self.Error(sys.exc_info())
            return Result(result=result, error=error)      

        result, error = self.Error(sys.exc_info())
        return Result(result=result, error=error)   
    


    def GetHost(self, host):
        """
        find out ip or hostname of given host to access hosts/devices which do not appear in DNS but
        have their ip saved in Nagios
        """

        # the fasted method is taking hostname as used in monitor
        if str(self.conf.connect_by_host) == "True" or host == "":
            return Result(result=host)
        
        # initialize ip string
        ip = ""

        # glue nagios cgi url and hostinfo 
        nagcgiurl_host  = self.monitor_cgi_url + "/extinfo.cgi?type=1&host=" + host
        
        # get host info
        result = self.FetchURL(nagcgiurl_host, giveback="obj")
        htobj = result.result

        try:
            # take ip from html soup
            ip = htobj.findAll(name="div", attrs={"class":"data"})[-1].text    

            # workaround for URL-ified IP as described in SF bug 2967416
            # https://sourceforge.net/tracker/?func=detail&aid=2967416&group_id=236865&atid=1101370
            if not ip.find("://") == -1:
                ip = ip.split("://")[1]
                
            # print IP in debug mode
            if str(self.conf.debug_mode) == "True":    
                self.Debug(server=self.get_name(), host=host, debug ="IP of %s:" % (host) + " " + ip)
            # when connection by DNS is not configured do it by IP
            if str(self.conf.connect_by_dns) == "True":
                # try to get DNS name for ip, if not available use ip
                try:
                    address = socket.gethostbyaddr(ip)[0]
                except:
                    address = ip
            else:
                address = ip
        except:
            result, error = self.Error(sys.exc_info())
            return Result(result=result, error=error)
         
        # do some cleanup
        del htobj    

        # give back host or ip
        return Result(result=address)

    
    def Hook(self):
        """
        allows to add some extra actions for a monitor server to be executed in RefreshLoop
        inspired by Centreon and its seemingly Alzheimer desease regarding session ID/Cookie/whatever
        """
        # do some garbage collection
        gc.collect()  
        
    
    def Error(self, error):
        """
        Handle errors somehow - print them or later log them into not yet existing log file
        """
        if str(self.conf.debug_mode) == "True":
            debug = ""
            for line in traceback.format_exception(error[0], error[1], error[2], 5):
                debug += line
            self.Debug(server=self.get_name(), debug=debug, head="ERROR")
            
        return ["ERROR", traceback.format_exception_only(error[0], error[1])[0]]
    
    
    def Debug(self, server="", host="", service="", debug="", head="DEBUG"):
        """
        centralized debugging
        """
        debug_string =  " ".join((head + ":",  str(datetime.datetime.now()), server, host, service, debug))     
        # give debug info to debug loop for thread-save log-file writing
        self.debug_queue.put(debug_string)
        
    
