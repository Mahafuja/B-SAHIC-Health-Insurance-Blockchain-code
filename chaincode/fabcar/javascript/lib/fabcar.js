/*
 * Copyright IBM Corp. All Rights Reserved.
 *
 * SPDX-License-Identifier: Apache-2.0
 */

'use strict';

const { Contract } = require('fabric-contract-api');

class FabCar extends Contract {

    async initLedger(ctx) {
        console.info('============= START : Initialize Ledger ===========');
        const cars = [
            {
                age: 30,
                gender: 'Female',
                contact: '+8801704876175',
                medical_history: 'High Blood pressure',
                ssn: '4645271760',
                name: 'Alice',
                occupation: 'CEO of ALICO',
                nominees_details: 'Manha',
                lifetimesupport: 5000,
                currentoperations: [],
                pendingclaims: [],
                thisyear: 0,
                thisyearmoney: 0,
                issue_date: '27-07-2021',
                maturity_date: '27-07-2071',
                plan_year: '50',
                address: 'Los Angeles'
            },
            {
                age: 40,
                gender: 'Male',
                contact: '+8801918544788',
                medical_history: 'Healthy',
                ssn: '1992436710231',
                name: 'Bob',
                occupation: 'MD of ALICO',
                nominees_details: 'Simlan',
                lifetimesupport: 10000,
                currentoperations: [],
                pendingclaims: [],
                thisyear: 0,
                thisyearmoney: 0,
                issue_date: '20-08-2021',
                maturity_date: '20-08-2071',
                plan_year: '50',
                address: 'New York'
            },
            {
                age: 60,
                gender: 'Male',
                contact: '+8801918544788',
                medical_history: 'High blood pressure',
                ssn: '19920982710231',
                name: 'Boris',
                occupation: 'Dorector of ALICO',
                nominees_details: 'Johnson',
                lifetimesupport: 8000,
                currentoperations: [],
                pendingclaims: [],
                thisyear: 0,
                thisyearmoney: 0,
                issue_date: '01-09-2021',
                maturity_date: '01-09-2071',
                plan_year: '50',
                address: 'Boston'
            },

        ];

        for (let i = 0; i < cars.length; i++) {
            cars[i].docType = 'Client';
            let temp = i + 10000000000000;
            await ctx.stub.putState(temp.toString(), (JSON.stringify(cars[i])));
            console.info('Added <--> ', cars[i]);
        }
        console.info('============= END : Initialize Ledger ===========');
    }




    async cashless(ctx, policy_no, treatmentapplyingfor) { // add treatment record to a user
        var treatementprice = [200, 300, 400, 500, 600, 200000];
        var availpercentage = [10, 20, 30, 40, 50, 5];
        treatmentapplyingfor = treatmentapplyingfor - 3011;
        //check if user is in the network
        const carAsBytes = await ctx.stub.getState(policy_no);

        if (!carAsBytes || carAsBytes.length === 0) {
            return (`Failed to submit transaction! Client ID: ${policy_no} does not exist`); //Client is not in the network
        }
        var result = carAsBytes.toString();
        result = JSON.parse(result);
        const car = JSON.parse(carAsBytes.toString());
        //check if the client is already in the same treatment operation say if he/she has taken treatment type A but woun't be able to claim for treatment B.
        if (car.currentoperations.includes(treatmentapplyingfor) == true) {
            return (`Client ID: ${policy_no} has already applied for this treatment.`);
        }
        //check if client has enough money left in their lifetime support from the insurance company
        if (result.lifetimesupport >= ((treatementprice[treatmentapplyingfor]) * availpercentage[treatmentapplyingfor] / 100)) {
            //Client has enough money


            //car.lifetimesupport = ((car.lifetimesupport) - treatementprice[treatmentapplyingfor]);
            car.currentoperations.push(treatmentapplyingfor);

            await ctx.stub.putState(policy_no, (JSON.stringify(car)));
            return ("Registration for this treatment has been successfully completed.");
        }
        else {
            return ("User is registered, but either does not have enough money, or invalid treatment type chosen. So no changes made!");
        }
    }

    async claimrequest(ctx, policy_no, treatmentapplyingfor, claimid) { // add treatment record to a user
        var treatementprice = [200, 300, 400, 500, 600, 200000];
        var availpercentage = [10, 20, 30, 40, 50, 5];
        treatmentapplyingfor = treatmentapplyingfor - 3011;
        //check if user is in the network
        const carAsBytes = await ctx.stub.getState(policy_no);
        if (!carAsBytes || carAsBytes.length === 0) {

            return (`Failed to submit transaction! Client ID: ${policy_no} does not exist`); //Client is not in the network
            //After throw the function terminates
        }

        var result = carAsBytes.toString();
        result = JSON.parse(result);
        const car = JSON.parse(carAsBytes.toString());
        //check if client treatment record exists
        if (car.thisyear >= 3) {
            return (`Client ID: ${policy_no} has already reached yearly quota.`);
        }
        if (car.thisyearmoney >= 2000) {
            return (`Client ID: ${policy_no} has already reached yearly quota on money.`);
        }

        if (car.currentoperations.includes(treatmentapplyingfor) == true) {
            //Client treatment record exists
            console.log("loading claim...\n");
            car.pendingclaims.push(claimid);
            var i = 0;
            while (i < car.currentoperations.length) {
                if (car.currentoperations[i] === treatmentapplyingfor) {
                    car.currentoperations.splice(i, 1);
                }
                else {
                    ++i;
                }
            }
            await ctx.stub.putState(policy_no, (JSON.stringify(car)));
            return ("Claim request has been successfully placed, and insurance company will be notified.");
        }
        else {
            return ("User is registered, but treatment record does not exist, claim unsuccessful! So no changes made!");
        }
    }

    async claimapprove(ctx, policy_no, claimid, resulta) { // add treatment record to a user
        var treatementprice = [200, 300, 400, 500, 600, 200000];
        var availpercentage = [10, 20, 30, 40, 50, 5];
        var claimidstr = claimid.split("-");
        var treatmentapplyingfor = claimidstr[0] - 3011;
        //check if user is in the network
        const carAsBytes = await ctx.stub.getState(policy_no);
        if (!carAsBytes || carAsBytes.length === 0) {
            return (`Failed to submit transaction! Client ID: ${policy_no} does not exist`); //Client is not in the network
            //After throw the function terminates
        }
        var result = carAsBytes.toString();
        result = JSON.parse(result);
        const car = JSON.parse(carAsBytes.toString());
        //check if client claimid exists
        if (car.pendingclaims.includes(claimid) == true) {
            //Client claimid exists
            if (resulta == 1) {
                var amountgranted = ((treatementprice[treatmentapplyingfor]) * availpercentage[treatmentapplyingfor] / 100);
                if (amountgranted > (2000 - car.thisyearmoney)) { //Partial claim will be granted, since otherwise yearly limit will be crossed.
                    amountgranted = 2000 - car.thisyearmoney;
                }
                car.thisyear = car.thisyear + 1;
                car.lifetimesupport = ((car.lifetimesupport) - amountgranted);
                car.thisyearmoney = car.thisyearmoney + amountgranted;
            }
            var i = 0;
            while (i < car.pendingclaims.length) {
                if (car.pendingclaims[i] === claimid) {
                    car.pendingclaims.splice(i, 1);
                } else {
                    ++i;
                }
            }

            await ctx.stub.putState(policy_no, (JSON.stringify(car)));
            if (resulta == 0) {
                return ("User is registered, and treatment record exists, but claim has been declined!");
            }
            return ("Claim has been granted to the healthcare.");
        }
        else {
            return ("User is registered, but treatment record does not exist, claim unsuccessful! So no changes made!");
        }
    }

    async createCar(ctx, policynumber, ssn, name, age, gender, contact, medical_history, occupation, nominees_details, issue_date, maturity_date, plan_year, address) {
        console.info('============= START : Create Policy ===========');
        const clientdata = {
            ssn,
            docType: 'Client',
            name,
            age,
            gender,
            contact,
            medical_history,
            occupation,
            nominees_details,
            lifetimesupport: 5000,
            currentoperations: [],
            pendingclaims: [],
            thisyear: 0,
            thisyearmoney: 0,
            issue_date,
            maturity_date,
            plan_year,
            address
        };
        await ctx.stub.putState(policynumber, (JSON.stringify(clientdata)));
        console.info('============= END : Create Policy ===========');
        return (policynumber);
        // while (true) {
        //     var carAsBytes = await ctx.stub.getState(nextfreepolicyno.toString());
        //     if (!carAsBytes || carAsBytes.length === 0) {
        //         break;
        //     }
        //     else {
        //         nextfreepolicyno = nextfreepolicyno + 1;
        //     }
        // }
        // await ctx.stub.putState(policy_no, (JSON.stringify(asset)));
        // return JSON.stringify(asset);
    }


    async queryCar(ctx, policy_no) {
        const carAsBytes = await ctx.stub.getState(policy_no); // get the car from chaincode state
        if (!carAsBytes || carAsBytes.length === 0) {
            return (`Client ID: ${policy_no} does not exist`);
        }
        console.log(carAsBytes.toString());
        return carAsBytes.toString();
    }



    async AssetExists(ctx, id) {
        const assetJSON = await ctx.stub.getState(id);
        return assetJSON && assetJSON.length > 0;
    }
    // DeleteAsset deletes an given asset from the world state. 
    async DeleteAsset(ctx, policy_no) {
        const exists = await this.AssetExists(ctx, policy_no);
        if (!exists) {
            throw new Error(`The asset ${policy_no} does not exist`);
        }
        return ctx.stub.deleteState(policy_no);
    }




    async queryAllCars(ctx) {
        const startKey = '';
        const endKey = '';
        const allResults = [];
        for await (const { key, value } of ctx.stub.getStateByRange(startKey, endKey)) {
            const strValue = (value).toString('utf8');
            let record;
            try {
                record = JSON.parse(strValue);
            } catch (err) {
                console.log(err);
                record = strValue;
            }
            allResults.push({ Key: key, Record: record });
        }
        console.info(allResults);
        return JSON.stringify(allResults);
    }

    async changeCarOwner(ctx, policy_no, newOwner) {
        console.info('============= START : changePolicyOwner ===========');

        const carAsBytes = await ctx.stub.getState(policy_no); // get the car from chaincode state
        if (!carAsBytes || carAsBytes.length === 0) {
            return (`Client ID: ${policy_no} does not exist`);
        }
        const car = JSON.parse(carAsBytes.toString());
        car.owner = newOwner;

        await ctx.stub.putState(policy_no, (JSON.stringify(car)));
        console.info('============= END : changePolicyOwner ===========');
    }

}

module.exports = FabCar;
