<?php
     function DateDiff($strDate1,$strDate2)
     {
                return (strtotime($strDate2) - strtotime($strDate1))/  ( 60 * 60 * 24 );  // 1 day = 60*60*24
     }
     function TimeDiff($strTime1,$strTime2)
     {
                return (strtotime($strTime2) - strtotime($strTime1))/  ( 60 * 60 ); // 1 Hour =  60*60
     }
     function DateTimeDiff($strDateTime1,$strDateTime2)
     {
                return (strtotime($strDateTime2) - strtotime($strDateTime1))/  ( 60 * 60 ); // 1 Hour =  60*60
     }
     function TimeMinuteDiff($strTime1,$strTime2)
     {
                return (strtotime($strTime2) - strtotime($strTime1))/  60; // 1 Hour =  60*60
     }
?>