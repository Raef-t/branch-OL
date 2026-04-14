import 'package:flutter/material.dart';
import '/core/components/subject_name_in_menu_card_component.dart';
import '/core/components/three_details_cards_inside_menu_card_component.dart';
import '/core/sized_boxs/heights.dart';

class LeftSideInsideMenuCardComponent extends StatelessWidget {
  const LeftSideInsideMenuCardComponent({
    super.key,
    required this.subjectName,
    required this.course,
    required this.classRoom,
    required this.type,
  });
  final String subjectName, course, classRoom, type;
  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.end,
      children: [
        SubjectNameInMenuCardComponent(subjectName: subjectName),
        Heights.height22(context: context),
        ThreeDetailsCardsInsideMenuCardComponent(
          course: course,
          classRoom: classRoom,
          type: type,
        ),
      ],
    );
  }
}
