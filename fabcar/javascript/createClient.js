/*
 * Copyright IBM Corp. All Rights Reserved.
 *
 * SPDX-License-Identifier: Apache-2.0
 */

'use strict';

const { Gateway, Wallets } = require('fabric-network');
const fs = require('fs');
const prompt = require('prompt-sync')();
const path = require('path');
//const readline = require('readline').createInterface({
//input: process.stdin,
//  output: process.stdout
//})
async function main() {
    try {
        // load the network configuration
        const ccpPath = path.resolve(__dirname, '..', '..', 'test-network', 'organizations', 'peerOrganizations', 'org1.example.com', 'connection-org1.json');
        let ccp = JSON.parse(fs.readFileSync(ccpPath, 'utf8'));

        // Create a new file system based wallet for managing identities.
        const walletPath = '/home/whoami/hyperledger_serverside/fabcar/javascript/wallet';
        const wallet = await Wallets.newFileSystemWallet(walletPath);
        //console.log(`Wallet path: ${walletPath}`);

        // Check to see if we've already enrolled the user.
        const identity = await wallet.get('appUser');
        if (!identity) {
            console.log('An identity for the user "appUser" does not exist in the wallet');
            console.log('Run the registerUser.js application before retrying');
            return;
        }

        // Create a new gateway for connecting to our peer node.
        const gateway = new Gateway();
        await gateway.connect(ccp, { wallet, identity: 'appUser', discovery: { enabled: true, asLocalhost: true } });

        // Get the network (channel) our contract is deployed to.
        const network = await gateway.getNetwork('mychannel');

        // Get the contract from the network.
        const contract = network.getContract('fabcar');

        var currentoperations = [];
        var pendingclaims = [];
        //var 
        var userid = await contract.submitTransaction('createCar', policynumber, ssn, name, age, gender, mobileno, medical_history, occupation, nominees_details, issue_date, maturity_date, plan_year, address);
        console.log('User has been successfully added, and is registered with Client ID: ' + userid);

        // Disconnect from the gateway.
        await gateway.disconnect();

    } catch (error) {
        console.error(`Failed to submit transaction: ${error}`);
        process.exit(1);
    }
}


//const name = prompt('What is your name? ');
//console.log(name);
var arrge = process.argv;
const policynumber = (arrge[2]);
const ssn = (arrge[3]);
const name = (arrge[4]);
const age = (arrge[5]);
const gender = (arrge[6]);
const mobileno = (arrge[7]);
const medical_history = (arrge[8]);
const occupation = (arrge[9]);
const nominees_details = (arrge[10]);
const issue_date = (arrge[11]);
const maturity_date = (arrge[12]);
const plan_year = (arrge[13]);
const address = (arrge[14]);
main();
