import 'package:flutter/material.dart';
import '/core/components/list_tile_details_and_attendance_to_student_component.dart';
import '/features/details_students/presentation/managers/models/details_students/details_students_model.dart';

class SuccessStateForDetailsAndAttendanceToStudentComponent
    extends StatelessWidget {
  const SuccessStateForDetailsAndAttendanceToStudentComponent({
    super.key,
    required this.detailsStudentsModel,
  });
  final DetailsStudentsModel detailsStudentsModel;
  @override
  Widget build(BuildContext context) {
    return ListTileDetailsAndAttendanceToStudentComponent(
      studentPhoto: detailsStudentsModel.studentPhoto ?? '',
      studentName: detailsStudentsModel.studentName ?? 'لا يوجد اسم طالب',
      batchName:
          detailsStudentsModel.detailsStudentsBatchModel?.batchName ??
          'لا يوجد اسم شعبه',
      studentAttendance: detailsStudentsModel.studentAttendance == true,
    );
  }
}
