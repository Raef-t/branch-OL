import 'package:flutter/material.dart';
import '/core/components/leading_list_tile_details_and_attendance_component.dart';
import '/core/components/subtitle_list_tile_details_and_attendance_component.dart';
import '/core/components/title_list_tile_details_and_attendance_component.dart';
import '/core/components/trailing_list_tile_details_and_attendance_component.dart';

class ListTileDetailsAndAttendanceToStudentComponent extends StatelessWidget {
  const ListTileDetailsAndAttendanceToStudentComponent({
    super.key,
    required this.studentPhoto,
    required this.studentName,
    required this.batchName,
    required this.studentAttendance,
  });
  final String studentPhoto, studentName, batchName;
  final bool studentAttendance;
  @override
  Widget build(BuildContext context) {
    return Directionality(
      textDirection: TextDirection.rtl,
      child: ListTile(
        leading: LeadingListTileDetailsAndAttendanceComponent(
          studentPhoto: studentPhoto,
        ),
        title: TitleListTileDetialsAndAttendanceComponent(
          studentName: studentName,
        ),
        subtitle: SubtitleListTileDetailsAndAttendanceComponent(
          batchName: batchName,
        ),
        trailing: TrailingListTileDetailsAndAttendanceComponent(
          studentAttendance: studentAttendance,
        ),
      ),
    );
  }
}
