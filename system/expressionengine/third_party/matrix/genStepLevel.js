/*
 *The MIT License (MIT)
 *
 *Copyright (c) 2013 Paul Sijpkes.
 *
 *Permission is hereby granted, free of charge, to any person obtaining a copy
 *of this software and associated documentation files (the "Software"), to deal
 *in the Software without restriction, including without limitation the rights
 *to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *copies of the Software, and to permit persons to whom the Software is
 *furnished to do so, subject to the following conditions:
 *
 *The above copyright notice and this permission notice shall be included in
 *all copies or substantial portions of the Software.
 *
 *THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *THE SOFTWARE.
 */
window.saveStepStr = "";
window.saveLevelStr = "";
window.saveStepArr = [];
window.saveLevArr = [];

/* @TODO: allow removal of items as well */

var genStepLevel = function(o) {
    if ($.inArray(o.step, saveStepArr) == -1) {
        if (saveStepArr.length > 0)
            saveStepStr += ", ";

        saveStepStr += stepDefinitions[o.step == 0 ? 8 : o.step];
        saveStepArr.push(o.step);
    }
    
    /* Levels only apply for steps higher then 0 (Being a Professional)*/
    if (o.step > 0) {
        switch(o.level) {
            case 1:
                levelName = 'Emerging';
                break;
            case 2:
                levelName = 'Consolidating';
                break;
            case 3:
                levelName = "Competent to Graduate";
        }

        if ($.inArray(levelName, saveLevArr) == -1) {
            if (saveLevArr.length > 0)
                saveLevelStr += ", ";

            saveLevelStr += levelName;
            saveLevArr.push(levelName);
        }
    }
};
