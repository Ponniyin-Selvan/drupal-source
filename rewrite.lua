function file_exists(path)
   local attr = lighty.stat(path)
   if (attr and attr["is_file"]) then
      return true
   else
      return false
   end
end

function removePrefix(str, prefix)
   return str:sub(1,#prefix+1) == prefix.."/" and str:sub(#prefix+2)
end

function string.starts(x,y)
   return string.sub(x,1,string.len(y)) == y
end

local prefix = ''

if (not file_exists(lighty.env["physical.path"]))  then   -- if this is not an existing file
    lighty.header["Expires"] = "Sun, 19 Nov 1978 05:00:00 GMT"
    lighty.header["Cache-Control"] = "no-store, no-cache, must-revalidate, post-check=0, pre-check=0"
    local FINAL_PATH = "empty"
    local req_uri = removePrefix(lighty.env["uri.path"], prefix)
    --print ("URI "..req_uri)
    local query_string = lighty.env["uri.query"]
    local SERVE_CACHED_FLAG = (string.find((lighty.request["Cookie"] or "NOCOOKIE"), "DRUPAL_UID") == nil) and (lighty.env["request.method"] == "GET") -- 1st filter (anon+no_uriquery+GET) to qualify boost caching
    if (SERVE_CACHED_FLAG == true) then  -- qualifies for boost cache type
        local boost_path_uribase = lighty.env["physical.doc-root"] .. "/cache/" .. lighty.request["Host"] .. "/" .. req_uri .. "_"
        local gzip_boost_path_uribase = lighty.env["physical.doc-root"] .. "/cache/gz/" .. lighty.request["Host"] .. "/" .. req_uri .. "_"
        if (query_string) then
           boost_path_uribase = boost_path_uribase ..  query_string
           gzip_boost_path_uribase = gzip_boost_path_uribase .. query_string
        end
        local RESTRICTED_PATHS = (string.starts(req_uri,"/admin")) 
                              or (string.starts(req_uri,"/cache")) 
                              or (string.starts(req_uri,"/misc"))
                              or (string.starts(req_uri,"/modules"))
                              or (string.starts(req_uri,"/sites"))
                              or (string.starts(req_uri,"/system"))
                              or (string.starts(req_uri,"/themes"))
                              
        accept_encoding = lighty.request["Accept-Encoding"] or "no_acceptance"
        if (RESTRICTED_PATHS == false) then
			if ((string.find(accept_encoding, "gzip")) and file_exists(gzip_boost_path_uribase .. ".html.gz")) then
				FINAL_PATH = gzip_boost_path_uribase .. ".html.gz"
				lighty.header["Content-Encoding"] = "gzip"
				lighty.header["Content-Type"] = ""				
			elseif (file_exists(boost_path_uribase .. ".html")) then
				FINAL_PATH = boost_path_uribase .. ".html"
			end
         end
     end
     if (FINAL_PATH == "empty") then       -- file still missing. pass it to the fastcgi backend
        local request_uri = removePrefix(lighty.env["uri.path"], prefix)
        if request_uri then
          lighty.env["uri.path"]          = prefix .. "/index.php"
          local uriquery = lighty.env["uri.query"] or ""
          lighty.env["uri.query"] = uriquery .. (uriquery ~= "" and "&" or "") .. "q=" .. request_uri
          lighty.env["physical.rel-path"] = lighty.env["uri.path"]
          lighty.env["request.orig-uri"]  = lighty.env["request.uri"]
          FINAL_PATH = lighty.env["physical.doc-root"] .. lighty.env["physical.rel-path"]
        end
     else
     	print("Final Path "..FINAL_PATH)
     end
     --print("Final Path Query "..lighty.env["uri.query"])
     lighty.env["physical.path"] = FINAL_PATH
end
