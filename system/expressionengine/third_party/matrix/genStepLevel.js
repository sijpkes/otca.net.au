window.saveStepStr = "";
window.saveLevelStr = "";
window.saveStepArr = [];
window.saveLevArr = [];

var genStepLevel = function(o) {
    if($.inArray(o.step, saveStepArr) == -1) {
        if(saveStepArr.length > 0) saveStepStr += ", ";
       
        saveStepStr += stepDefinitions[o.step == 0 ? 8 : o.step];
        saveStepArr.push(o.step);
    }
    switch(o.level) {
            case 1:
                levelName='Emerging';
            break;
            case 2:
                levelName='Consolidating';
            break;
            case 3:
                levelName = "Competent to Graduate";
    } 
    
    if($.inArray(levelName, saveLevArr) == -1) {
        if(saveLevArr.length > 0) saveLevelStr += ", ";
        
        saveLevelStr += levelName;   
        saveLevArr.push(levelName);
    }
};
