import 'package:flutter/cupertino.dart';
import '/core/border_radius/circulars.dart';
import '/core/components/app_bar_widget_with_right_arrow_image_and_three_texts_component.dart';
import '/core/components/background_body_to_views_component.dart';
import '/core/components/list_tile_details_and_attendance_to_student_component.dart';
import '/core/components/sliver_app_bar_to_hole_app_component.dart';
import '/core/components/svg_image_component.dart';
import '/core/components/text_medium12_component.dart';
import '/core/components/text_medium14_component.dart';
import '/core/decorations/box_decorations.dart';
import '/core/helpers/responsive_text_helper.dart';
import '/core/paddings/padding_with_child/only_padding_with_child.dart';
import '/core/paddings/padding_without_child/only_padding_without_child.dart';
import '/core/paddings/padding_without_child/symmetric_padding_without_child.dart';
import '/core/sized_boxs/heights.dart';
import '/core/sized_boxs/widths.dart';
import '/core/styles/colors_style.dart';
import '/core/styles/texts_style.dart';
import '/gen/assets.gen.dart';
import '/gen/fonts.gen.dart';

class CustomFilterAttendanceViewBody extends StatelessWidget {
  const CustomFilterAttendanceViewBody({super.key});

  @override
  Widget build(BuildContext context) {
    return CustomScrollView(
      slivers: [
        const SliverAppBarToHoleAppComponent(
          appBarWidget: AppBarWidgetWithRightArrowImageAndThreeTextsComponent(
            firstText: 'A11',
            secondText: 'يمكنك الاطلاع على حالة التفقد التابعة',
            thirdText: 'للشعبه',
          ),
        ),
        SliverFillRemaining(
          hasScrollBody: false,
          child: BackgroundBodyToViewsComponent(
            child: Column(
              children: [
                Heights.height17(context: context),
                const ListTileDetailsAndAttendanceToStudentComponent(
                  studentPhoto: '',
                  studentName: 'لا يوجد',
                  batchName: 'لا يوجد',
                  studentAttendance: true,
                ),
                Heights.height37(context: context),
                OnlyPaddingWithChild.right22(
                  context: context,
                  child: Align(
                    alignment: Alignment.centerRight,
                    child: Text(
                      'فبراير 18، 2025',
                      style: TextStyle(
                        fontSize: responsiveTextHelper(
                          fontSize: 20,
                          context: context,
                        ),
                        fontFamily: FontFamily.tajawal,
                        fontWeight: FontWeight.w500,
                        color: ColorsStyle.littleBlackColor,
                      ),
                    ),
                  ),
                ),
                Heights.height38(context: context),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceAround,
                  children: List.generate(7, (index) {
                    return Container(
                      padding:
                          OnlyPaddingWithoutChild.top8AndBottom4AndRight14AndLeft14(
                            context: context,
                          ),
                      decoration:
                          BoxDecorations.boxDecorationToFullDateCardSelectedAndUnSelectedComponent(
                            context: context,
                            color: ColorsStyle.whiteColor,
                          ),
                      child: Column(
                        children: [
                          Text(
                            '18',
                            style: TextsStyle.semiBold16(context: context),
                          ),
                          Text(
                            'Mon',
                            style: TextsStyle.medium12(context: context),
                          ),
                        ],
                      ),
                    );
                  }),
                ),
                Heights.height37(context: context),
                Container(
                  margin: OnlyPaddingWithoutChild.right20AndLeft20AndBottom21(
                    context: context,
                  ),
                  padding:
                      SymmetricPaddingWithoutChild.horizontal27AndVertical9(
                        context: context,
                      ),
                  decoration: BoxDecoration(
                    color: ColorsStyle.whiteColor,
                    borderRadius: Circulars.circular10(context: context),
                  ),
                  child: Row(
                    children: [
                      SvgImageComponent(
                        pathImage: Assets.images.manyCircleAvatarsImage,
                        color: ColorsStyle.greenColor2,
                      ),
                      const Spacer(),
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.end,
                        children: [
                          const TextMedium14Component(
                            text: 'السبت',
                            color: ColorsStyle.mediumBlackColor2,
                          ),
                          Heights.height9(context: context),
                          Row(
                            children: [
                              Column(
                                crossAxisAlignment: CrossAxisAlignment.end,
                                children: [
                                  const TextMedium12Component(
                                    text: '12/3/2025',
                                    fontFamily: 'Tajawal',
                                    color: ColorsStyle.mediumBrownColor,
                                  ),
                                  Heights.height10(context: context),
                                  const TextMedium12Component(
                                    text: 'وقت الوصول 2:00',
                                    fontFamily: 'Tajawal',
                                    color: ColorsStyle.mediumBrownColor,
                                  ),
                                  Heights.height10(context: context),
                                  const TextMedium12Component(
                                    text: 'وقت الانصراف 2:00',
                                    fontFamily: 'Tajawal',
                                    color: ColorsStyle.mediumBrownColor,
                                  ),
                                ],
                              ),
                              Widths.width10(context: context),
                              Column(
                                children: [
                                  Assets.images.dateImage.image(),
                                  Heights.height15(context: context),
                                  Assets.images.watchImage.image(),
                                  Heights.height15(context: context),
                                  Assets.images.watchImage.image(),
                                ],
                              ),
                            ],
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ),
      ],
    );
  }
}
