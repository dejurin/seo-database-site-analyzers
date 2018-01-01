"use strict"

const tinyreq = require("tinyreq"),
    cheerio = require("cheerio"),
    url = require("url"),
    fs = require('fs'),
    path = require('path'),
    lines = fs.readFileSync(__dirname + '/list.txt', 'utf-8').split('\n').filter(Boolean);
var _url = "http://example.com";

function _par(link, _hostname, callback) {
    tinyreq({
        url: link,
        headers: {
            "user-agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.108 Safari/537.36"
        }
    }).then(body => {
        let $ = cheerio.load(body);
        var done = false;
        var msg;
        if($("a").length > 0) {
            $("a").each(function(i, elem) {
                if($(this).attr("href")) {
                    if(url.parse($(this).attr("href")).hostname == _hostname) {
                        done = true;
                    }
                }
            });
        }
        callback([done, link]);
    }).catch(err => {
        console.log(err);
    });
};
for(var i = 0; i < lines.length; i++) {
    var _hostname = url.parse(_url).hostname;
    var _protocol = url.parse(_url).protocol;
    var _anchor = _hostname;
    var link = lines[i].replace("{PROTOCOL}", _protocol + "//").replace("{HOST}", _hostname).replace("{ANCHOR}", _anchor);
    // console.log("Get: " + link);
    var p = _par(link, _hostname, function(response) {
        if(response[0]) {
            fs.appendFileSync(__dirname + "/output.txt", response[1] + "\n", {
                encoding: "utf8"
            });
        }
        console.log(((response[0]) ? "+" : "-") + "\t" + response[1]);
    });
};
