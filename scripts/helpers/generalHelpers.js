const os = require('os')

const env = process.env.NODE_ENV === 'DEVELOPMENT' ? 'dev' : 'prod'
const isMacOS = os.platform() === 'darwin'

module.exports = { env, isMacOS }
