'use strict';

var config = require('./config');


var MOUNT_PATH = config.ini.MOUNT_PATH;


var addMiddleware = function (connect, options, middlewares) {
  middlewares.unshift(
    require('grunt-connect-rewrite/lib/utils').rewriteRequest,
    require('grunt-connect-proxy/lib/utils').proxyRequest,
    require('gateway')(options.base[0], {
      '.php': 'php-cgi',
      'env': {
        'PHPRC': 'node_modules/hazdev-template/dist/conf/php.ini'
      }
    })
  );
  return middlewares;
};


var connect = {
  options: {
    hostname: '*'
  },

  proxies: [
    {
      context: '/data', // data on external server
      host: config.ini.DATA_HOST,
      port: 80,
      headers: {
        'accept-encoding': 'identity',
        host: config.ini.DATA_HOST
      },
      rewrite: {
        '^/data': MOUNT_PATH + '/data'
      }
    },
    {
      context: '/theme/',
      host: 'localhost',
      port: config.templatePort,
      rewrite: {
        '^/theme': ''
      }
    }
  ],

  rules: [
    {
      from: '^(' + MOUNT_PATH + ')(.*)/+$',
      to: 'http://localhost:' + config.buildPort + '$1$2', // strip final '/'
      redirect: 'permanent'
    },
    {
      from: '^' + MOUNT_PATH + '/(2hr|24hr)/([0-9]+)/([0-9]{8}|latest)/([0-9]{2})$',
      to: '/spectrogram.php?timespan=$1&id=$2&date=$3&hour=$4'
    },
    {
      from: '^' + MOUNT_PATH + '/(2hr|24hr)/([0-9]+)/([0-9]{8}|latest)$',
      to: '/spectrogram.php?timespan=$1&id=$2&date=$3'
    },
    {
      from: '^' + MOUNT_PATH + '/(2hr|24hr)/([0-9]{8}|latest)$',
      to: '/spectrograms.php?timespan=$1&date=$2'
    },
    {
      from: '^' + MOUNT_PATH + '/(2hr|24hr)/([0-9]+)$',
      to: '/spectrograms.php?timespan=$1&id=$2'
    },
    {
      from: '^' + MOUNT_PATH + '/(2hr|24hr)$',
      to: '/index.php?timespan=$1'
    },
    {
      from: '^' + MOUNT_PATH + '/?(.*)$',
      to: '/$1'
    }
  ],

  dev: {
    options: {
      base: [
        config.build + '/' + config.src + '/htdocs'
      ],
      livereload: config.liveReloadPort,
      middleware: addMiddleware,
      open: 'http://localhost:' + config.buildPort + MOUNT_PATH + '/24hr',
      port: config.buildPort
    }
  },

  dist: {
    options: {
      base: [
        config.dist + '/htdocs'
      ],
      port: config.distPort,
      keepalive: true,
      open: 'http://localhost:' + config.distPort + MOUNT_PATH + '/24hr',
      middleware: addMiddleware
    }
  },

  /*example: {
    options: {
      base: [
        config.example,
        config.build + '/' + config.src + '/htdocs',
        config.etc
      ],
      middleware: addMiddleware,
      open: 'http://localhost:' + config.examplePort + '/example.php',
      port: config.examplePort
    }
  },*/

  template: {
    options: {
      base: [
        'node_modules/hazdev-template/dist/htdocs'
      ],
      port: config.templatePort,
      middleware: addMiddleware
    }
  }/*,

  test: {
    options: {
      base: [
        config.build + '/' + config.src + '/htdocs',
        config.build + '/' + config.test,
        config.etc,
        'node_modules'
      ],
      port: config.testPort,
      open: 'http://localhost:' + config.testPort + '/test.html'
    }
  }*/
};


module.exports = connect;
